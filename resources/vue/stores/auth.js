import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user")) || null,
        token: localStorage.getItem("token") || null,
        isAuthenticated: !!localStorage.getItem("token"),
        department: JSON.parse(localStorage.getItem("department")) || null,
        role: JSON.parse(localStorage.getItem("role")) || [],
        permissions: JSON.parse(localStorage.getItem("permissions")) || [],
        pageTitle: "",
    }),

    actions: {
        async login(email, password) {
            try {
                // Fetch CSRF cookie without baseURL interference
                await window.axios.get("/sanctum/csrf-cookie", {
                    baseURL: window.location.origin  // Use full origin, not /api
                });
            } catch (error) {
                console.warn("CSRF cookie fetch failed:", error);
            }

            const response = await window.axios.post("/api/login", {
                email,
                password,
            });

            if (response.status === 200) {
                // Make sure roles and permissions exist; fallback to empty array
                this.user = response.data.user;
                this.department = response.data.user.dept_name || null;
                this.role = response.data.role || [];
                this.permissions = response.data.permissions || [];
                this.token = response.data.token;
                this.isAuthenticated = true;

                console.log(response.data)

                localStorage.setItem("token", this.token);
                localStorage.setItem("user", JSON.stringify(this.user));
                localStorage.setItem("role", JSON.stringify(this.role));
                localStorage.setItem("permissions", JSON.stringify(this.permissions));
                localStorage.setItem("department", JSON.stringify(this.department));
            }

            return response;
        },

        async logout() {
            const response = await window.axios.post("/api/logout");

            if (response.status === 200) {
                this.user = null;
                this.role = [];
                this.permissions = [];
                this.token = null;
                this.isAuthenticated = false;

                localStorage.removeItem("token");
                localStorage.removeItem("user");
                localStorage.removeItem("role");
                localStorage.removeItem("permissions");
                localStorage.removeItem("department");
            }

            return response;
        },
    },
})
