<script setup>
import { ref, computed } from "vue";

// Reactive variables
const message = ref("");
const phone_number = ref("+251921607264"); // Default phone number
const status = ref("");
const loading = ref(false);

// Computed property for status styling
const statusClass = computed(() => {
  if (status.value.includes("Failed") || status.value.includes("Error")) {
    return "error-message";
  } else if (status.value.includes("successfully")) {
    return "success-message";
  }
  return "";
});

// Send SMS function
const sendSMS = async () => {
  if (!message.value.trim()) {
    status.value = "Please enter a message";
    return;
  }

  if (!phone_number.value.trim()) {
    status.value = "Please enter a phone number";
    return;
  }

  loading.value = true;
  status.value = "";

  try {
    const response = await fetch("/api/send-sms", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
      body: JSON.stringify({
        to: phone_number.value, // This matches the API validation
        message: message.value,
      }),
    });

    const result = await response.json();

    if (response.ok) {
      if (result.success) {
        status.value = "SMS sent successfully!";
        message.value = ""; // Clear message after successful send
      } else {
        status.value = `Failed to send SMS: ${result.error}`;
      }
    } else {
      status.value = `Error: ${result.message || "Unknown error occurred"}`;
    }
  } catch (error) {
    status.value = "Network error: " + error.message;
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="sms-test-container">
    <h1>Test SMS Notification</h1>
    <div class="sms-form">
      <input
        v-model="message"
        placeholder="Enter SMS message"
        :disabled="loading"
      />
      <input
        v-model="phone_number"
        placeholder="Enter phone number (e.g., +251921607264)"
        :disabled="loading"
      />
      <button
        @click="sendSMS"
        :disabled="loading || !message.trim()"
        :class="{ loading: loading }"
      >
        {{ loading ? "Sending..." : "Send SMS" }}
      </button>
      <p v-if="status" :class="statusClass">{{ status }}</p>
    </div>
  </div>
</template>

<style scoped>
.sms-test-container {
  text-align: center;
  padding: 20px;
  max-width: 500px;
  margin: 0 auto;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.sms-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  align-items: center;
}

input {
  padding: 12px;
  width: 100%;
  max-width: 350px;
  border: 2px solid #e1e5e9;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

input:focus {
  outline: none;
  border-color: #4caf50;
}

input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

button {
  padding: 12px 24px;
  background-color: #4caf50;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
  transition: all 0.3s ease;
  min-width: 120px;
}

button:hover:not(:disabled) {
  background-color: #45a049;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
}

button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

button.loading {
  position: relative;
}

.success-message {
  color: #4caf50;
  font-weight: 500;
}

.error-message {
  color: #f44336;
  font-weight: 500;
}

p {
  margin: 0;
  padding: 8px;
  border-radius: 4px;
  min-height: 20px;
}

.success-message {
  background-color: #e8f5e8;
}

.error-message {
  background-color: #fdeaea;
}

h1 {
  color: #333;
  margin-bottom: 30px;
  font-size: 24px;
  font-weight: 600;
}
</style>
