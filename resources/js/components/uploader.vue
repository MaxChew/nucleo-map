<template>
  <div>
    <label class="btn  btn-file" :class="{ loading: loading }">
      {{ placeholder }}
      <input type="file" :disabled="loading" :multiple="multiple" @change="handleUpload" ref="input" />
    </label>
    <span v-if="loading" class="text-muted ml-2 px-2"
      v-text="uploadPercent >= 100 ? 'Processing...' : `Uploading...${uploadPercent}%`"></span>
    <span v-else-if="verrors.has(name)" class="text-red ml-2 px-2" v-text="verrors.first(name)"></span>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import ErrorBag from './../libs/ErrorBag';
import axios from 'axios';

export default {
  props: {
    multiple: Boolean,
    name: {
      type: String,
      default: 'upload',
    },
    url: String,
    placeholder: {
      type: String,
      default: 'File Upload',
    },
    bufferSeconds: {
      type: Number,
      default: 3,
    },
    maxSize: {
      type: Number,
      default: 10485760,
    },
  },
  setup(props, { emit }) {
    const verrors = ref(new ErrorBag());
    const uploadPercent = ref(0);
    const loading = ref(false);
    const dummyProgressInterval = 200;
    const inputRef = ref(null);

    const postName = computed(() => props.multiple ? `${props.name}[]` : props.name);
    const bufferPercent = computed(() => Math.round((props.bufferSeconds * 1000) / dummyProgressInterval));

    const handleUpload = (e) => {
      let formData = new FormData();
      let files = Array.from(e.target.files);


      // Allowed file formats
      const allowedFormats = ["image/jpeg", "image/jpg", "image/png", "image/gif"];

      // Filter out invalid file types
      files = files.filter(file => {
        if (!allowedFormats.includes(file.type)) {
          emit('invalid');
          return false;
        }
        return true;
      });

      // Check if any valid files remain
      if (!files.length) return;

      // Validate file size
      if (props.maxSize) {
        files = files.filter(file => {
          if (file.size > props.maxSize) {
            alert(`The size of the file "${file.name}" exceeds the maximum size allowed (${props.maxSize} bytes)`);
            return false;
          }
          return true;
        });
      }

      verrors.value.clear(props.name);

      loading.value = true;
      files.forEach(file => formData.append(postName.value, file));
      let dummyProgressTimer;
      axios.post(props.url, formData, {
        onUploadProgress: progress => {
          uploadPercent.value = Math.round((progress.loaded * (100 - bufferPercent.value)) / progress.total);
          if (progress.loaded >= progress.total && uploadPercent.value < 100) {
            dummyProgressTimer = setInterval(() => {
              uploadPercent.value = uploadPercent.value >= 100 ? 100 : uploadPercent.value + 1;
            }, dummyProgressInterval);
          }
        },
      }).then(response => {
        if (dummyProgressTimer) clearInterval(dummyProgressTimer);
        uploadPercent.value = 100;
        e.target.value = null;
        emit('success', response.data);
        loading.value = false;
      }).catch(error => {
        if (dummyProgressTimer) clearInterval(dummyProgressTimer);
        e.target.value = null;
        const { message, detail, response } = error;
        if (response && response.status === 422) {
          verrors.value.record(response.data.data);
          emit('invalid', verrors.value.toListItem());
          loading.value = false;
          return;
        }
        console.log("error");
        emit('failed', error);
        loading.value = false;
      });
    };

    return {
      verrors,
      uploadPercent,
      loading,
      dummyProgressInterval,
      postName,
      bufferPercent,
      handleUpload,
      inputRef,
    };
  },
};
</script>