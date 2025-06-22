<template>
  <div
    class="whiteboard flex flex-col items-center justify-center relative rounded-lg"
    :style="{
      backgroundImage: `url(${backgroundUrl})`,
      backgroundRepeat: 'no-repeat',
      backgroundPosition: 'center',
      backgroundColor: `${backgroundColor}`,
      backgroundSize: 'cover',
    }"
    style="padding-top: calc(calc(760 / 1080) * 100%);"
  >
    <div v-if="loading" class="loading-text">Loading...</div>
    <div v-else class="absolute top-5 left-0 w-full h-full">
      <transition name="custom-fade" mode="out-in">
        <img
          v-if="signatures.length > 0"
          :src="signatures[currentIndex].signature"
          alt="Signature"
          :key="signatures[currentIndex].id"
          class="signature-image"
        />
      </transition>
    </div>
    <div v-if="newSignatureDetected" class="new-signature-notice"></div>
    <div class="bg-white w-full h-12 flex items-center justify-center absolute top-full left-0">
      <span v-text="'Welcome, ' + signatures[currentIndex].attendance.participant.name" v-if="signatures[currentIndex]"></span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    apiUrl: {
      type: String,
      required: true,
    },
    interval: {
      type: Number,
      default: 60000, // Set to 1 minute
    },
    displayInterval: {
      type: Number,
      default: 8000, // Set to 10 seconds
    },
    backgroundUrl: {
      type: String,
      default: 'https://i.pinimg.com/1200x/ec/5c/d9/ec5cd9f0428c94b39a271f0d73fa5e50.jpg',
    },
    backgroundColor: {
      type: String,
      default: '#000000',
    },
  },
  data() {
    return {
      signatures: [],
      loading: true,
      currentIndex: 0,
      newSignatureDetected: false,
      fetchIntervalId: null,
      rotateTimeoutId: null,
    };
  },
  mounted() {
    console.log('API URL on load:', this.apiUrl);
    this.fetchSignatures(); // First time fetch
    this.fetchIntervalId = setInterval(this.fetchSignatures, this.interval); // Fetch signatures every interval
    this.scheduleNextRotation();
  },
  beforeUnmount() {
    clearInterval(this.fetchIntervalId);
    clearTimeout(this.rotateTimeoutId);
  },
  methods: {
    async fetchSignatures() {
      try {
        this.loading = true;
        console.log('Fetching from API URL:', this.apiUrl);
        const response = await axios.get(this.apiUrl);
        const newSignatures = response.data.data;

        if (Array.isArray(newSignatures)) {
          this.checkForNewSignatures(newSignatures);
          this.signatures = newSignatures;
          this.currentIndex = 0; // Reset currentIndex on new data
          console.log('Fetched signatures:', this.signatures);
        } else {
          console.log('Expected an array but got:', newSignatures);
        }
      } catch (error) {
        console.log('Error fetching signatures:', error);
      } finally {
        this.loading = false;
      }
    },
    scheduleNextRotation() {
      this.rotateTimeoutId = setTimeout(this.rotateSignatures, this.displayInterval);
    },
    rotateSignatures() {
      if (this.signatures.length > 0) {
        this.currentIndex = (this.currentIndex + 1) % this.signatures.length;
        console.log('Change new signature:', this.signatures[this.currentIndex].id);
        this.scheduleNextRotation(); // Schedule next rotation
      }
    },
    checkForNewSignatures(newSignatures) {
      const existingSignatureIds = this.signatures.map((s) => s.id);
      const newSignatureIds = newSignatures.map((s) => s.id);
      this.newSignatureDetected = newSignatureIds.some((id) => !existingSignatureIds.includes(id));

      if (this.newSignatureDetected) {
        setTimeout(() => {
          this.newSignatureDetected = false;
        }, 5000);
      }
    },
  },
};
</script>

<style scoped>
.whiteboard {
  border: 2px solid #ccc; /* Border to mimic a whiteboard frame */
  width: 100%; /* Adjust size as needed */
  height: 100%; /* Adjust size as needed */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional shadow for depth */
  position: relative;
  display: flex;
  align-items: center; /* Center vertically */
  justify-content: center; /* Center horizontally */
}

.signature-image {
  display: block;
  margin: 0 auto;
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
}

.loading-text {
  font-size: 1.5em;
  color: #666;
}

.new-signature-notice {
  position: absolute;
  bottom: 10px;
  font-size: 1.2em;
  color: red;
}

/* Custom fade transition */
.custom-fade-enter-active,
.custom-fade-leave-active {
  transition: opacity 1s, transform 1s;
}
.custom-fade-enter,
.custom-fade-leave-to /* .custom-fade-leave-active in <2.1.8 */ {
  opacity: 0;
  transform: perspective(500px) rotateY(90deg) scale(0.5) translateX(50%);
}
</style>