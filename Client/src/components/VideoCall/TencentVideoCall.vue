<script setup>
import { ref, onMounted } from "vue";
import {
  TUICallKit,
  TUICallKitServer,
  TUICallType,
} from "@tencentcloud/call-uikit-vue";
import * as GenerateTestUserSig from "@/debug/GenerateTestUserSig-es";

const SDKAppID = 40000719;
const SDKSecretKey =
  "17eb724f5184648ae6651277d82740750a9adb698cb23848a8c29939bbbaad82";

const props = defineProps({
  currentUserId: { type: String, required: true },
  peerUserId: { type: String, required: true },
  autoCall: { type: Boolean, default: false },
  isCaller: { type: Boolean, default: false },
});

const initialized = ref(false);

const init = async () => {
  const { userSig } = GenerateTestUserSig.genTestUserSig({
    userID: props.currentUserId,
    SDKAppID,
    SecretKey: SDKSecretKey,
  });
  await TUICallKitServer.init({
    userID: props.currentUserId,
    userSig,
    SDKAppID,
  });
  initialized.value = true;
};

const call = async () => {
  await TUICallKitServer.calls({
    userIDList: [props.peerUserId],
    type: TUICallType.VIDEO_CALL,
  });
};

onMounted(async () => {
  await init();
  if (props.autoCall && props.isCaller) {
    await call();
  }
});
</script>

<template>
  <TUICallKit style="width: 100%; height: 100%" />
</template>
