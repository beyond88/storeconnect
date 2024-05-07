<template>
  <div id="storeconnect-sync-manage">
    <div id="sync-status-manage" class="sync-status-manage"></div>
    <div class="sync-action-manage">
      <div class="lds-spinner" id="sync-spinner" v-if="showSpinner">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>

      <button
        v-if="!syncInProgress"
        id="storeconnect-order-sync-btn"
        type="button"
        class="button"
        @click="startSync"
      >
        Sync now
      </button>
      <button
        v-if="syncInProgress"
        id="storeconnect-order-stop-sync-btn"
        type="button"
        class="button"
        @click="stopSync"
      >
        <span>Stop sync</span>
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "App",
  components: {},
  data() {
    return {
      showSpinner: false,
      baseURL: window.location.origin,
      syncStatusInterval: null,
      syncInProgress: false,
    };
  },
  mounted() {
    this.checkIfSyncPage();
    this.pingSyncStatus();
  },
  methods: {
    async checkIfSyncPage() {
      let params = new URLSearchParams(window.location.search);

      if (
        params.get("page") === "wc-settings" &&
        params.get("tab") === "settings_tab_storeconnect"
      ) {
        this.syncStatusInterval = setInterval(this.pingSyncStatus, 5000);
      } else {
        this.hideSyncSpinner();
      }
    },
    async pingSyncStatus() {
      try {
        this.showSyncSpinner();
        const response = await axios.post(
          `${this.baseURL}/wp-json/storeconnect/v1/sync-status`,
          {},
          {}
        );
        console.log("Sync status:", response);
        if (response.data.success) {
          const status = response.data.status;
          this.syncInProgress = status === "in_progress";
          if (!this.syncInProgress) {
            clearInterval(this.syncStatusInterval);
            this.hideSyncSpinner();
          }
        } else {
          clearInterval(this.syncStatusInterval);
          this.hideSyncSpinner();
        }
      } catch (error) {
        console.error("Sync error:", error);
        console.error(
          "Error updating order status and adding note:",
          error.response.data
        );
        this.hideSyncSpinner();
      }
    },
    showSyncSpinner() {
      this.showSpinner = true;
    },
    hideSyncSpinner() {
      this.showSpinner = false;
    },
    async startSync() {
      try {
        this.showSyncSpinner();
        const response = await axios.post(
          `${this.baseURL}/wp-json/storeconnect/v1/start-sync`,
          {},
          {}
        );
        console.log("Sync started:", response);
        if(response.data.success) {
          if(parseInt(response.data.response.remaining_orders) > 0) {
            this.showSyncSpinner();
          }
        }
      } catch (error) {
        console.error("Sync error:", error);
        console.error(
          "Error updating order status and adding note:",
          error.response.data
        );
      } finally {
        this.hideSyncSpinner();
      }
    },
    async stopSync() {
      try {
        this.showSyncSpinner();
        const response = await axios.post(
          `${this.baseURL}/wp-json/storeconnect/v1/stop-sync`,
          {},
          {}
        );
        console.log("Sync stopped:", response);
      } catch (error) {
        console.error("Sync error:", error);
        console.error(
          "Error updating order status and adding note:",
          error.response.data
        );
      } finally {
        this.hideSyncSpinner();
      }
    },
  },
};
</script>

<style>
/* Your component styles here */
</style>
