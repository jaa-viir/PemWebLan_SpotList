import { defineStore } from "pinia";

export const useFlashStore = defineStore("flash", {
	state: () => ({
		message: "",
		type: "success", // 'success' | 'error'
	}),
	actions: {
		show(message, type = "success") {
			this.message = message;
			this.type = type;
			setTimeout(() => {
				this.message = "";
			}, 3000);
		},
		success(message) {
			this.show(message, "success");
		},
		error(message) {
			this.show(message, "error");
		},
	},
});
