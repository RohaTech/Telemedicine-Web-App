<script setup>
// lang='ts'
import { ref } from "vue";
import {
  TUICallKit,
  TUICallKitServer,
  TUICallType,
} from "@tencentcloud/call-uikit-vue";
import * as GenerateTestUserSig from "@/debug/GenerateTestUserSig-es"; // Refer to Step 2.3

//【3】Make a 1v1 video call
const call = async () => {
  await TUICallKitServer.calls({
    userIDList: [calleeUserID.value],
    type: TUICallType.VIDEO_CALL,
  });
};

const SDKAppID = 40000719; // TODO: Replace with your SDKAppID (Notice: SDKAppID is of type number）
const SDKSecretKey =
  "17eb724f5184648ae6651277d82740750a9adb698cb23848a8c29939bbbaad82"; // TODO: Replace with your SDKSecretKey

const callerUserID = ref("");
const calleeUserID = ref("");

//【2】Initialize the TUICallKit component
const init = async () => {
  const { userSig } = GenerateTestUserSig.genTestUserSig({
    userID: callerUserID.value,
    SDKAppID,
    SecretKey: SDKSecretKey,
  });
  await TUICallKitServer.init({
    userID: callerUserID.value,
    userSig,
    SDKAppID,
  });
  alert("TUICallKit init succeed");
};
</script>

<template>
  <span> caller's ID: </span>
  <input type="text" v-model="callerUserID" />
  <button @click="init">step1. init</button> <br />
  <span> callee's ID: </span>
  <input type="text" v-model="calleeUserID" />
  <button @click="call">step2. call</button>

  <!--【1】Import the TUICallKit component: Call interface UI -->
  <TUICallKit style="width: 650px; height: 500px" />
</template>
