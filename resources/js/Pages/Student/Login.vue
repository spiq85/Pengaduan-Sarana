<script setup>
import { ref } from "vue";
import { useForm, Head } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const form = useForm({
    username: "",
    password: "",
});

const isPasswordVisible = ref(false);
const toggleVisibility = () => {
    isPasswordVisible.value = !isPasswordVisible.value;
};

const submit = () => {
    form.post("/student/login", {
        onFinish: () => form.reset("password"),
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "LOGIN GAGAL!",
                text: "Username atau password kamu salah, cuy.",
                borderRadius: "2rem",
                confirmButtonColor: "#0f172a",
            });
        },
    });
};
</script>

<template>
    <Head title="Login Siswa - Portal Aspirasi" />

    <div
        class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden font-sans"
    >
        <div
            class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-200/30 rounded-full blur-[100px]"
        ></div>

        <div class="max-w-md w-full relative z-10 px-4">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center h-16 w-16 bg-blue-600 rounded-2xl shadow-xl shadow-blue-200 mb-4 rotate-3 italic"
                >
                    <i class="fas fa-rocket text-white text-2xl"></i>
                </div>
                <h1
                    class="text-3xl font-black text-slate-800 tracking-tighter uppercase italic"
                >
                    Student <span class="text-blue-600">Center</span>
                </h1>
            </div>

            <div
                class="bg-white border border-slate-100 p-10 rounded-[2.5rem] shadow-2xl shadow-slate-200/50"
            >
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2"
                            >Username</label
                        >
                        <div class="relative group">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"
                                ><i class="fas fa-user text-sm"></i
                            ></span>
                            <input
                                v-model="form.username"
                                type="text"
                                class="block w-full bg-slate-50 border border-slate-100 text-slate-700 pl-12 pr-4 py-4 rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all placeholder:text-slate-300 font-bold text-sm"
                                placeholder="Username Siswa"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2"
                            >Password</label
                        >
                        <div class="relative group">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"
                                ><i class="fas fa-lock text-sm"></i
                            ></span>
                            <input
                                v-model="form.password"
                                :type="isPasswordVisible ? 'text' : 'password'"
                                class="block w-full bg-slate-50 border border-slate-100 text-slate-700 pl-12 pr-12 py-4 rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all placeholder:text-slate-300 font-bold text-sm"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="toggleVisibility"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-blue-600 transition-colors px-2"
                            >
                                <i
                                    :class="
                                        isPasswordVisible
                                            ? 'fas fa-eye-slash'
                                            : 'fas fa-eye'
                                    "
                                ></i>
                            </button>
                        </div>
                    </div>

                    <button
                        :disabled="form.processing"
                        class="w-full bg-slate-900 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] py-5 rounded-2xl transition-all shadow-xl shadow-slate-200 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
                    >
                        <span v-if="form.processing"
                            ><i class="fas fa-circle-notch animate-spin"></i>
                            Loading...</span
                        >
                        <span v-else
                            >Masuk Sekarang <i class="fas fa-arrow-right"></i
                        ></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
