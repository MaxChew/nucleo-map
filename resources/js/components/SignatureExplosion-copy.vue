<template>
  <div class="signature-container bg-gradient-to-b from-blue-200 to-blue-50" ref="signatureRef" 
  :style="computedStyle">

    <!-- 添加全屏按钮 -->
    <button 
      @click="toggleFullscreen" 
      class="fullscreen-button !absolute bottom-2 left-2 z-10 transition-opacity duration-300"
      :class="{ 
        'opacity-100': !isFullscreen || isButtonVisible, 
        'opacity-0': isFullscreen && !isButtonVisible 
      }"
    >
      {{ isFullscreen ? 'Exit Full Screen' : 'Full Screen' }}
    </button>
    <!-- 错误状态显示 -->
    <div v-if="error" class="error-overlay">
      <div class="error-message">
        {{ error }}
        <button @click="retryInitialization" class="retry-button">
          重试
        </button>
      </div>
    </div>
    
    <!-- 加载状态 -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner"></div>
      <div class="loading-text">加载中...</div>
    </div>
    
    <!-- Canvas -->
    <canvas ref="canvas" class="w-full h-full" :style="{ minHeight: '400px' }"></canvas>
    
    <!-- 调试面板 - 只在 debug 模式下显示 -->
    <div  class="debug-panel">
      <div>新签名: {{ newSignatures.length }}</div>
      <div>旧签名: {{ oldSignatures.length }}</div>
      <div>状态: {{ isShowingNewSignature ? '展示新签名' : '展示旧签名' }}</div>
      <div>FPS: {{ fps }}</div>
      <div>容器宽度: {{ containerSize.width }}px</div>
      <div>容器高度: {{ containerSize.height }}px</div>
      <div>缩放比例: {{ containerSize.scale.toFixed(3) }}</div>
    </div>
    
    <AlertSystem />
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, inject, computed, nextTick } from 'vue';
import * as THREE from 'three';
import { gsap } from 'gsap';
import axios from 'axios';

export default {
  name: 'SignatureAnimation',
  props: {
    checkInterval: {
      type: Number,
      default: 15000
    },
    apiUrl: {
      type: String,
      required: true,
    },
    debug: {
      type: Boolean,
      default: false
    },
    backgroundUrl: {
      type: String,
      default: null
    },
    backgroundColor: {
      type: String,
      // 默认使用 Tailwind 的渐变色
      default: 'linear-gradient(to bottom, rgb(191 219 254), rgb(239 246 255))'  // bg-gradient-to-b from-blue-200 to-blue-50
    },
    useDemoEffect: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    // 基础设置
    const canvas = ref(null);
    const signatureRef = ref(null);
    const loading = ref(true);
    const error = ref(null);  // 添加错误状态
    const fps = ref(0);       // 添加FPS计数器
    const alert = inject('alert', null);
    const isFullscreen = ref(false);
    const showFullscreenButton = ref(false);
    let buttonHideTimeout = null;
    const isButtonVisible = ref(false);  // 添加这行
    const containerSize = ref({
      width: 0,
      height: 0,
      scale: 0
    });


    // 签名数据管理
    const newSignatures = ref([]);
    const oldSignatures = ref([]);
    const isShowingNewSignature = ref(false);

    // Three.js 场景管理
    let scene, camera, renderer;
    let animationFrameId = null;
    let checkIntervalId = null;

    // FPS计算相关
    let lastTime = performance.now();
    let frameCount = 0;

    const updateFPS = () => {
      const currentTime = performance.now();
      frameCount++;
      
      if (currentTime - lastTime >= 1000) {
        fps.value = Math.round(frameCount * 1000 / (currentTime - lastTime));
        frameCount = 0;
        lastTime = currentTime;
      }
    };

    // 2. 性能优化函数
    const optimizeRendering = () => {
      let rafId = null;
      
      return () => {
        if (rafId) return;
        
        rafId = requestAnimationFrame(() => {
          OldSignatureManager.update();
          renderer.render(scene, camera);
          updateFPS();  // 更新FPS
          rafId = null;
        });
      }
    };

    // 现在可以安全地创建优化后的渲染函数
    const optimizedRender = optimizeRendering();
    
    const animate = () => {
      animationFrameId = requestAnimationFrame(animate);
      optimizedRender();
    };

    // 重试初始化方法
    const retryInitialization = async () => {
      error.value = null;
      await initialize();
    };

    // 初始化流程
    const initialize = async () => {
      try {
        loading.value = true;
        
        await initThree();
        await loadSignatures();
        
        window.addEventListener('resize', handleResize);
        animate();
        checkIntervalId = setInterval(checkForNewSignatures, props.checkInterval);
        
      } catch (err) {
        console.error('初始化失败:', err);
        error.value = '初始化失败，请重试';
        alert?.error('初始化失败，请刷新页面重试');
      } finally {
        loading.value = false;
      }
    };

    // 创建基础场景
    const initThree = () => {
      if (!canvas.value) {
        throw new Error('Canvas element not found');
      }

      scene = new THREE.Scene();
      
      camera = new THREE.PerspectiveCamera(
        50,
        signatureRef.value.clientWidth / signatureRef.value.clientHeight,
        0.1,
        1000
      );

      camera.position.set(0, 0, 5);  // 相机位置调整
      camera.lookAt(0, 0, -10);     // 相机朝向调整

      renderer = new THREE.WebGLRenderer({
        canvas: canvas.value,
        antialias: true,
        alpha: true
      });
      
      renderer.setSize(signatureRef.value.clientWidth, signatureRef.value.clientHeight);
      renderer.setPixelRatio(window.devicePixelRatio);
    };

    // 签名精灵创建工具
    // 创建新签名（带背景）
    // 创建新签名（带背景）
    const createNewSignatureSprite = async (signatureData) => {
      if (!signatureData) {
        throw new Error('签名数据不能为空');
      }

      try {
        // 创建主组
        const group = new THREE.Group();
        
        // 创建签名精灵
        const textureLoader = new THREE.TextureLoader();
        const imageData = signatureData.signature || signatureData.image_url || '/images/default-signature.png';
        
        let texture;
        if (typeof imageData === 'string' && imageData.includes('base64')) {
          const image = await loadBase64Image(imageData);
          texture = new THREE.Texture(image);
          texture.needsUpdate = true;
        } else {
          texture = await textureLoader.loadAsync(imageData);
        }

        const signatureMaterial = new THREE.SpriteMaterial({
          map: texture,
          transparent: true,
          opacity: 1
        });

        const signatureSprite = new THREE.Sprite(signatureMaterial);

        // 基础缩放设置
        const baseScale = 0.8;
        signatureSprite.scale.set(baseScale, baseScale, 1);
        
        // 添加到组中
        group.add(signatureSprite);

        return { 
          group,           // 主组
          signatureSprite, // 签名精灵
        };

      } catch (error) {
        console.error('创建签名精灵失败:', error);
        throw error;
      }
    };

    // 创建普通签名（不带背景，用于旧签名）
    const createSignatureSprite = async (signatureData) => {
      try {
        const textureLoader = new THREE.TextureLoader();
        const imageData = signatureData.signature || signatureData.image_url || '/images/default-signature.png';
        
        let texture;
        if (typeof imageData === 'string' && imageData.includes('base64')) {
          const image = await loadBase64Image(imageData);
          texture = new THREE.Texture(image);
          texture.needsUpdate = true;
        } else {
          texture = await textureLoader.loadAsync(imageData);
        }

        const material = new THREE.SpriteMaterial({
          map: texture,
          transparent: true,
          opacity: 1
        });

        return new THREE.Sprite(material);
      } catch (error) {
        console.error('创建签名精灵失败:', error);
        throw error;
      }
    };

    

    // 旧签名管理器
    const OldSignatureManager = {
      sprites: [],
      visible: true, // 添加可见性控制

      setVisibility(visible) {
        this.visible = visible;
        // 设置所有精灵的可见性
        this.sprites.forEach(sig => {
          sig.sprite.visible = visible;
        });
      },

      async addSignature(signatureData) {
        const sprite = await createSignatureSprite(signatureData);
        const signatureObj = {
          sprite,
          speed: 0.08 + Math.random() * 0.005,
          lane: Math.random() * 2 - 1,
          // 添加初始位置的随机性
          startPosition: {
            x: Math.random() * 6 - 3,  // 横向随机位置
            y: 0.5 + Math.random(),    // 高度随机
            z: -30 - Math.random() * 20 // 深度随机
          }
        };
        
        this.resetPosition(signatureObj);
        this.sprites.push(signatureObj);
        // 根据当前可见性状态设置新添加精灵的可见性
        sprite.visible = this.visible;
        scene.add(sprite);
        
        limitOldSignatures();
        
        return signatureObj;
      },

      update() {
        if (!this.visible) return;

        this.sprites.forEach(sig => {
          sig.sprite.position.z += sig.speed * 2;
          
          // 根据z轴位置计算缩放和透明度
          const progress = (sig.sprite.position.z + 30) / 40;
          const scale = 0.2 + progress * 2;
          sig.sprite.scale.set(scale, scale, scale);
          
          // 当签名接近视野边缘时淡出
          if (sig.sprite.position.z > 5) {
            sig.sprite.material.opacity = Math.max(0, 1 - (sig.sprite.position.z - 5) / 2);
          }
          
          // 当签名完全离开视野时重置位置
          if (sig.sprite.position.z > 8) {
            this.resetPosition(sig);
          }

          // 添加轻微的横向漂移
          sig.sprite.position.x += Math.sin(Date.now() * 0.001 + sig.lane) * 0.01;
        });
      },

      resetPosition(signatureObj, isInitial = false) {
        if (isInitial) {
          // 初始化时使用预设的随机位置
          signatureObj.sprite.position.set(
            signatureObj.startPosition.x,
            signatureObj.startPosition.y,
            signatureObj.startPosition.z
          );
        } else {
          // 循环播放时重置到起始位置
          const lanePosition = Math.random() * 6 - 3;  // 横向范围更大
          const startZ = -30 - Math.random() * 20;     // 纵深范围更大
          
          signatureObj.sprite.position.set(
            lanePosition,
            0.5 + Math.random() * 1.5,  // 高度范围更大
            startZ
          );
        }
        
        // 根据深度调整大小
        const distanceFromCamera = Math.abs(signatureObj.sprite.position.z + 30);
        const startScale = 0.2 + (distanceFromCamera / 60); // 远处的签名稍大一些
        signatureObj.sprite.scale.set(startScale, startScale, startScale);
        
        // 根据距离调整透明度
        signatureObj.sprite.material.opacity = 0.6 + (distanceFromCamera / 100);
      },

      clear() {
        this.sprites.forEach(sig => {
          scene.remove(sig.sprite);
          sig.sprite.material.dispose();
        });
        this.sprites = [];
      }
    };

    // 新签名管理器
    const NewSignatureManager = {
      currentSprite: null,
      isDestroyed: false,

      // 修改 calculateScreenSize 方法
      calculateScreenSize() {
        if (!signatureRef.value) return { width: 0, height: 0, scale: 0 };
        
        const container = signatureRef.value;
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;
        
        // 计算更小的缩放比例 (使用较小的尺寸的10%作为基准)
        let scale;
        if (containerWidth > containerHeight) {
          // 横屏时以高度为基准
          scale = 0.1 * (containerHeight / 1000); // 从20%改为10%
        } else {
          // 竖屏时以宽度为基准
          scale = 0.1 * (containerWidth / 1000); // 从20%改为10%
        }
        
        // 限制缩放范围，调小最大值
        scale = 10; // 调整最小和最大缩放范围
        
        console.log('计算得到的缩放比例:', scale);
        
        // 更新 containerSize ref
        containerSize.value = {
          width: containerWidth,
          height: containerHeight,
          scale: scale
        };
        
        return {
          scale: scale,
          width: containerWidth,
          height: containerHeight,
          targetSize: {
            width: containerWidth * scale,
            height: containerHeight * scale
          }
        };
      },

      async showNewSignature(signatureData) {
        this.isDestroyed = false;

        // 清理之前的精灵
        if (this.currentSprite) {
          try {
            if (scene && this.currentSprite.sprite) {
              scene.remove(this.currentSprite.sprite);
              if (this.currentSprite.sprite.material) {
                this.currentSprite.sprite.material.dispose();
              }
            }
          } catch (error) {
            console.warn('清理之前的精灵失败:', error);
          }
          this.currentSprite = null;
        }

        try {
          // 创建新签名 - 注意这里只用到 group 和 signatureSprite
          const { group, signatureSprite } = await createNewSignatureSprite(signatureData);
          
          if (this.isDestroyed || !scene) {
            if (group) {
              group.traverse(child => {
                if (child.material) child.material.dispose();
              });
            }
            return Promise.reject(new Error('Component destroyed'));
          }

          this.currentSprite = { sprite: group, data: signatureData };
          
          // 获取屏幕尺寸
          const screenSize = this.calculateScreenSize();
          
          // 设置初始位置和目标位置
          const startPosition = {
            x: 5,    // 从右侧开始
            y: 2,    // 略高一点
            z: 5     // 靠近镜头
          };

          const targetPosition = {
            x: 0,    // 中心
            y: 0,    // 中心
            z: -20   // 观察距离
          };

          // 设置初始状态
          group.position.set(startPosition.x, startPosition.y, startPosition.z);
          group.rotation.set(0, 0, 0);
          
          // 设置初始大小和透明度
          const initialScale = 15;
          signatureSprite.scale.set(initialScale, initialScale, initialScale);
          signatureSprite.material.opacity = 0;

          scene.add(group);

          return new Promise((resolve, reject) => {
            const timeline = gsap.timeline({
              onComplete: () => {
                try {
                  if (!this.isDestroyed && scene && group) {
                    scene.remove(group);
                    group.traverse(child => {
                      if (child.material) child.material.dispose();
                    });
                  }
                  this.currentSprite = null;
                  resolve();
                } catch (error) {
                  console.warn('清理精灵失败:', error);
                  reject(error);
                }
              }
            });

            timeline
              // 1. 淡入
              .to(signatureSprite.material, {
                opacity: 1,
                duration: 0.3,
                ease: "power2.in"
              })
              
              // 2. 飞行动画
              .to(group.position, {
                x: 0,
                y: targetPosition.y + 1.5,
                z: -5,
                duration: 1.5,
                ease: "power2.inOut"
              })
              .to(group.rotation, {
                y: Math.PI * 2,
                duration: 1.5,
                ease: "power2.inOut"
              }, "<")
              
              // 3. 降落到展示位置
              .to(group.position, {
                y: targetPosition.y,
                z: targetPosition.z,
                duration: 1,
                ease: "bounce.out"
              })
              
              // 4. 展示阶段 - 注意这里移除了bgAnimate的调用
              .to({}, {
                duration: 3,
                onUpdate: () => {
                  const time = Date.now() * 0.001;
                  
                  // 整体漂浮
                  group.position.y = targetPosition.y + Math.sin(time) * 0.3;
                  group.rotation.y = Math.PI * 2 + Math.sin(time * 0.5) * 0.05;
                }
              })
              // 5. 退场动画
              .to(group.position, {
                x: -5,
                y: targetPosition.y + 2,
                z: -50,
                duration: 2,
                ease: "power2.in"
              })
              .to(group.rotation, {
                y: Math.PI * 2.5,
                duration: 2,
                ease: "power2.in"
              }, "<")
              .to(signatureSprite.material, {
                opacity: 0,
                duration: 1.5,
                ease: "power2.in"
              }, "<");
          });

        } catch (error) {
          console.error('显示新签名失败:', error);
          return Promise.reject(error);
        }
      },

      // 添加窗口大小变化的处理
      handleResize() {
        if (this.currentSprite && this.currentSprite.sprite) {
          const sizeInfo = this.calculateScreenSize();
          console.log('屏幕信息:', sizeInfo);
          
          // 使用统一的缩放比例
          const scale = sizeInfo.scale;
          
          console.log('应用缩放比例:', scale);
          
          // 更新当前显示的签名大小
          gsap.to(this.currentSprite.sprite.scale, {
            x: scale,
            y: scale,
            z: scale,
            duration: 0.3,
            ease: "power2.out"
          });
        }
      },

      destroy() {
        this.isDestroyed = true;
        if (this.currentSprite) {
          try {
            if (scene && this.currentSprite.sprite) {
              scene.remove(this.currentSprite.sprite);
              if (this.currentSprite.sprite.material) {
                this.currentSprite.sprite.material.dispose();
              }
            }
          } catch (error) {
            console.warn('销毁精灵失败:', error);
          }
          this.currentSprite = null;
        }
      }
    };

    // 处理新签名队列
    const processNewSignatures = async () => {
      if (newSignatures.value.length === 0 || isShowingNewSignature.value) {
        // 如果没有新签名，显示旧签名
        OldSignatureManager.setVisibility(true);
        return;
      }
      
      // 有新签名时，隐藏旧签名
      OldSignatureManager.setVisibility(false);
      isShowingNewSignature.value = true;
      const signatureData = newSignatures.value[0];

      try {
        await NewSignatureManager.showNewSignature(signatureData);
        // 展示完成后，将签名添加到旧签名组
        await OldSignatureManager.addSignature(signatureData);
        newSignatures.value.shift();
      } catch (error) {
        console.error('处理新签名失败:', error);
      } finally {
        isShowingNewSignature.value = false;
        if (newSignatures.value.length > 0) {
          // 如果还有新签名，继续处理
          processNewSignatures();
        } else {
          // 没有新签名了，显示旧签名
          OldSignatureManager.setVisibility(true);
        }
      }
    };

    const checkForNewSignatures = async () => {
      try {
        const response = await axios.get(props.apiUrl + '?mode=unlimited&method=new');
        if (response.data.status === 'success' && response.data.data.length > 0) {
          newSignatures.value.push(...response.data.data);
          if (!isShowingNewSignature.value) {
            alert.success(`发现 ${newSignatures.value.length} 个新签名！`);
            // 有新签名时，先隐藏旧签名
            OldSignatureManager.setVisibility(false);
            processNewSignatures();
          }
        }
      } catch (error) {
        console.error('获取新签名失败:', error);
        if (alert) {
          alert.error('获取新签名失败: ' + (error.message || '未知错误'));
        }
      }
    };


    // 加载base64图片
    const loadBase64Image = (base64Data) => {
      return new Promise((resolve, reject) => {
        const image = new Image();
        image.onload = () => resolve(image);
        image.onerror = reject;
        if (!base64Data.startsWith('data:image')) {
          base64Data = 'data:image/png;base64,' + base64Data;
        }
        image.src = base64Data;
      });
    };

    // 窗口大小调整处理
    const handleResize = () => {
      if (!camera || !renderer || !signatureRef.value) return;
      
      const width = signatureRef.value.clientWidth;
      const height = signatureRef.value.clientHeight;
      
      // 更新 containerSize
      containerSize.value = {
        width: width,
        height: height,
        scale: containerSize.value.scale
      };
      
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);
      
      // 重新计算并更新签名大小
      NewSignatureManager.handleResize();
    };

    // 在 setup 函数内添加这个方法
    const initOldSignatures = async () => {
      try {
        // 获取旧签名数据
        const response = await axios.get(props.apiUrl + '?mode=unlimited&method=old');
        if (response.data.status === 'success' && response.data.data.length > 0) {
          // 将获取到的旧签名数据存储
          oldSignatures.value = response.data.data;
          
          // 为每个旧签名创建精灵
          for (const signatureData of oldSignatures.value) {
            await OldSignatureManager.addSignature(signatureData);
          }
        }
      } catch (error) {
        console.error('获取旧签名失败:', error);
        if (alert) {
          alert.error('获取旧签名失败: ' + (error.message || '未知错误'));
        }
      }
    };

    // 可以添加一个方法来控制最大显示的旧签名数量
    const MAX_OLD_SIGNATURES = 20; // 可以根据需求调整这个数值

    const limitOldSignatures = () => {
      if (OldSignatureManager.sprites.length > MAX_OLD_SIGNATURES) {
        const excess = OldSignatureManager.sprites.length - MAX_OLD_SIGNATURES;
        for (let i = 0; i < excess; i++) {
          const oldestSprite = OldSignatureManager.sprites.shift();
          scene.remove(oldestSprite.sprite);
          oldestSprite.sprite.material.dispose();
        }
      }
    };

    const loadSignatures = async () => {
      try {
        loading.value = true;
        await initOldSignatures();
      } catch (error) {
        console.error('加载签名失败:', error);
        alert?.error('加载签名失败，请刷新重试');
      } finally {
        loading.value = false;
      }
    }

    // 全屏切换函数
    const toggleFullscreen = async () => {
      try {
        if (!document.fullscreenElement) {
          // 进入全屏
          await signatureRef.value.requestFullscreen();
          isFullscreen.value = true;
          isButtonVisible.value = true;
          
          // 设置定时器在5秒后隐藏按钮
          startButtonHideTimer();
        } else {
          // 退出全屏
          await document.exitFullscreen();
          isFullscreen.value = false;
          isButtonVisible.value = true;
          
          // 清除隐藏定时器
          clearButtonHideTimer();
        }
      } catch (err) {
        console.error('全屏切换失败:', err);
      }
    };

    // 新增：专门处理定时器的方法
    const startButtonHideTimer = () => {
      clearButtonHideTimer();
      if (isFullscreen.value) {
        buttonHideTimeout = setTimeout(() => {
          if (isFullscreen.value) {
            isButtonVisible.value = false;
          }
        }, 5000);
      }
    };

    const clearButtonHideTimer = () => {
      if (buttonHideTimeout) {
        clearTimeout(buttonHideTimeout);
        buttonHideTimeout = null;
      }
    };

    // 监听全屏变化
    const handleFullscreenChange = () => {
      const wasFullscreen = isFullscreen.value;
      isFullscreen.value = !!document.fullscreenElement;
      
      if (!isFullscreen.value && wasFullscreen) {
        // 退出全屏时
        clearButtonHideTimer();
        isButtonVisible.value = true;
      } else if (isFullscreen.value && !wasFullscreen) {
        // 进入全屏时
        isButtonVisible.value = true;
        startButtonHideTimer();
      }

      // 全屏状态改变后重新计算尺寸
      nextTick(() => {
        NewSignatureManager.handleResize();
      });
    };


    // 鼠标移动显示/隐藏全屏按钮
    let timeoutId = null;

    // 处理鼠标移动显示/隐藏按钮的逻辑
    const handleMouseMove = () => {
      if (!isFullscreen.value) {
        isButtonVisible.value = true;
        return;
      }

      isButtonVisible.value = true;
      startButtonHideTimer();
    };

    const cleanup = () => {
      // 标记新签名管理器为已销毁状态
      NewSignatureManager.destroy();
      
      // 清理 Three.js 资源
      if (scene) {
        scene.traverse(object => {
          if (object.material) {
            object.material.dispose();
          }
          if (object.geometry) {
            object.geometry.dispose();
          }
        });
      }
      
      if (renderer) {
        renderer.dispose();
      }
    };

    const computedStyle = computed(() => {
      const style = {
        // 使用 Tailwind 的渐变色作为默认背景
        background: props.backgroundColor || 'linear-gradient(to bottom, rgb(191 219 254), rgb(239 246 255))'
      };

      // 如果有背景图片URL,则添加背景图片相关样式
      if (props.backgroundUrl || props.useDemoEffect) {
        Object.assign(style, {
          backgroundImage: `url(${computedBackgroundUrl.value})`,
          backgroundRepeat: 'no-repeat',
          backgroundPosition: 'center',
          backgroundSize: 'cover'
        });
      }

      return style;
    });

    const computedBackgroundUrl = computed(() => {
      if (props.backgroundUrl) {
        return props.backgroundUrl;
      } else if (props.useDemoEffect) {
        return '/images/board/demo_effect_dekstop_five.webp';
      }
      return null;
    });

    onMounted(() => {
      // 初始化时设置按钮可见
      isButtonVisible.value = true;
      isFullscreen.value = false;
      
      // 添加事件监听
      document.addEventListener('fullscreenchange', handleFullscreenChange);
      document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
      document.addEventListener('mozfullscreenchange', handleFullscreenChange);
      document.addEventListener('MSFullscreenChange', handleFullscreenChange);
      
      if (signatureRef.value) {
        signatureRef.value.addEventListener('mousemove', handleMouseMove);
      }
      
      // 添加鼠标移动监听
      if (signatureRef.value) {
        signatureRef.value.addEventListener('mousemove', handleMouseMove);
      }

      // 初始化时计算容器尺寸
      nextTick(() => {
        const size = NewSignatureManager.calculateScreenSize();
        containerSize.value = {
          width: size.width,
          height: size.height,
          scale: size.scale
        };
      });


      initialize();
    });
    
    onBeforeUnmount(() => {
      if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
      }
      if (checkIntervalId) {
        clearInterval(checkIntervalId);
      }
      cleanup();
      cleanup();
      // 清理事件监听
      document.removeEventListener('fullscreenchange', handleFullscreenChange);
      document.removeEventListener('webkitfullscreenchange', handleFullscreenChange);
      document.removeEventListener('mozfullscreenchange', handleFullscreenChange);
      document.removeEventListener('MSFullscreenChange', handleFullscreenChange);
      window.removeEventListener('resize', NewSignatureManager.handleResize);
      
      if (signatureRef.value) {
        signatureRef.value.removeEventListener('mousemove', handleMouseMove);
      }
      
      // 清理定时器
      if (buttonHideTimeout) {
        clearTimeout(buttonHideTimeout);
      }
    });

    
    return {
      canvas,
      signatureRef,
      loading,
      error,         // 添加错误状态
      fps,           // 添加 FPS
      debug: props.debug,  // 添加 debug 属性
      newSignatures,
      oldSignatures,
      isShowingNewSignature,
      retryInitialization,  // 添加重试方法
      computedStyle,  // 一定要返回 computedStyle
      computedBackgroundUrl,
      isFullscreen,
      isButtonVisible,
      toggleFullscreen,
      containerSize, 
      
    };
  },
};
</script>

<style scoped lang="scss">
.signature-container {  /* 修正类名前面的点 */
  @apply relative w-full ;
}

.fullscreen-toggle {
  position: absolute;
  bottom: 15px;
  left: 10px;
  z-index: 1000;
}

.fullscreen-button {
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  transition: all 0.3s ease;
  font-size: 0.8rem;
  @apply font-bold text-lg md:text-2xl;
  
  /* 非全屏状态下强制显示 */
  .signature-container:not(:fullscreen) & {
    opacity: 1 !important;
  }
  
  /* 全屏状态下的悬停效果 */
  :fullscreen & {
    &:hover {
      @apply opacity-100 bg-[#F77E0D];
    }
  }
}

/* 全屏状态下的悬停效果 */
:fullscreen .fullscreen-button:hover {
  @apply opacity-100 bg-[#F77E0D];
}

.debug-panel {  /* 添加 debug panel 样式 */
  @apply absolute top-4 left-4 p-2 bg-black/50 text-white text-sm rounded space-y-1;
}

/* 全屏样式 */
:fullscreen .signature-container {
  @apply w-screen h-screen;
}

:-webkit-full-screen .signature-container {
  @apply w-screen h-screen;
}

:-moz-full-screen .signature-container {
  @apply w-screen h-screen;
}

:-ms-fullscreen .signature-container {
  @apply w-screen h-screen;
}
</style>