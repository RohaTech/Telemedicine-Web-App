import { defineStore } from "pinia";
import router from "@/router";
export const useAuthStore = defineStore("authStore", {
  state: () => {
    return {
      user: null,
      errors: {},
    };
  },
  // getters: {},
  actions: {
    /********************* Get Authenticated User  ********************** */

    async getUser() {
      if (localStorage.getItem("token")) {
        try {
          const res = await fetch("/api/user", {
            headers: {
              authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          });
          const data = await res.json();
          if (res.ok) {
            this.user = data;
          } else {
            this.errors = { general: "Failed to fetch user data" };
          }
        } catch (error) {
          this.errors = {
            general: "An error occurred while fetching user data",
          };
          console.error(error);
        }
      }
    },

    /**************** Login and Register  ***************/
    async authenticate(apiRoute, formData) {
      const res = await fetch(`/api/${apiRoute}`, {
        method: "post",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      if (data.errors) {
        this.errors = data.errors;
      } else {
        console.log(this.user);
        this.errors = {};
        localStorage.setItem("token", data.token);
        this.user = data.user;
        router.push({
          name: data.user.role === "doctor" ? "DoctorStatus" : "Home",
        });
      }
    },

    /**************** Logout  ***************/

    async logout() {
      const res = await fetch("/api/logout", {
        method: "post",
        headers: {
          authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      const data = await res.json();
      console.log(data);

      if (res.ok) {
        this.user = null;
        this.errors = {};
        localStorage.removeItem("token");
        router.push({ name: "Home" });
      }
    },
  },
});
