<script setup>
import { ref } from "vue";
import { useForm, Head } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const form = useForm({
    username: "",
    password: "",
});

const showForgotModal = ref(false);
const forgotForm = useForm({
    nis: "",
    message: "",
});

const submitForgot = () => {
    forgotForm.post("/student/forgot-password", {
        onSuccess: () => {
            showForgotModal.value = false;
            forgotForm.reset();
            Swal.fire({
                icon: "success",
                title: "Terkirim!",
                text: "Permintaan reset password sudah dikirim ke admin.",
                confirmButtonColor: "#0f172a",
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: forgotForm.errors.nis || "Terjadi kesalahan.",
                confirmButtonColor: "#0f172a",
            });
        },
    });
};

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

    <div class="min-h-screen flex items-center justify-center relative overflow-hidden font-sans login-bg">
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="glow-orb glow-orb-a"></div>
            <div class="glow-orb glow-orb-b"></div>
            <div class="glow-orb glow-orb-c"></div>
            <div class="gradient-sheen"></div>
        </div>

        <div class="max-w-md w-full relative z-10 px-4">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center h-16 w-16 bg-blue-600 rounded-2xl shadow-xl shadow-blue-200 mb-4 rotate-3 italic">
                    <i class="fas fa-rocket text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter uppercase italic">
                    Student <span class="text-blue-600">Center</span>
                </h1>
            </div>

            <div class="bg-white border border-slate-100 p-10 rounded-[2.5rem] shadow-2xl shadow-slate-200/50">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Username</label>
                        <div class="relative group">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"><i
                                    class="fas fa-user text-sm"></i></span>
                            <input v-model="form.username" type="text"
                                class="block w-full bg-slate-50 border border-slate-100 text-slate-700 pl-12 pr-4 py-4 rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all placeholder:text-slate-300 font-bold text-sm"
                                placeholder="Username Siswa" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Password</label>
                        <div class="relative group">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"><i
                                    class="fas fa-lock text-sm"></i></span>
                            <input v-model="form.password" :type="isPasswordVisible ? 'text' : 'password'"
                                class="block w-full bg-slate-50 border border-slate-100 text-slate-700 pl-12 pr-12 py-4 rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all placeholder:text-slate-300 font-bold text-sm"
                                placeholder="••••••••" />
                            <button type="button" @click="toggleVisibility"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-blue-600 transition-colors px-2">
                                <i :class="isPasswordVisible
                                        ? 'fas fa-eye-slash'
                                        : 'fas fa-eye'
                                    "></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="button" @click="showForgotModal = true"
                            class="text-xs text-blue-600 hover:text-blue-800 font-bold">
                            Lupa Password?
                        </button>
                    </div>

                    <button :disabled="form.processing"
                        class="w-full bg-slate-900 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] py-5 rounded-2xl transition-all shadow-xl shadow-slate-200 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3">
                        <span v-if="form.processing"><i class="fas fa-circle-notch animate-spin"></i>
                            Loading...</span>
                        <span v-else>Masuk Sekarang <i class="fas fa-arrow-right"></i></span>
                    </button>
                </form>
            </div>
        </div>
        <Teleport to="body">
            <div v-if="showForgotModal"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                @click.self="showForgotModal = false">
                <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center h-14 w-14 bg-amber-100 rounded-2xl mb-3">
                            <i class="fas fa-key text-amber-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800">
                            Lupa Password?
                        </h3>
                        <p class="text-sm text-slate-400 mt-1">
                            Masukkan NIS kamu, admin akan mereset passwordnya.
                        </p>
                    </div>

                    <form @submit.prevent="submitForgot" class="space-y-4">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2 mb-1">
                                NIS
                            </label>
                            <input v-model="forgotForm.nis" type="text"
                                class="block w-full bg-slate-50 border border-slate-100 text-slate-700 px-4 py-3 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none font-bold text-sm"
                                placeholder="Masukkan NIS kamu" />
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2 mb-1">
                                Pesan (opsional)
                            </label>
                            <textarea v-model="forgotForm.message" rows="3"
                                class="block w-full bg-slate-50 border border-slate-100 text-slate-700 px-4 py-3 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none text-sm resize-none"
                                placeholder="Contoh: Saya lupa password akun saya..."></textarea>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showForgotModal = false"
                                class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-500 font-bold text-sm hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" :disabled="forgotForm.processing"
                                class="flex-1 py-3 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-blue-600 transition-colors disabled:opacity-50">
                                <span v-if="forgotForm.processing"><i class="fas fa-spinner animate-spin"></i></span>
                                <span v-else>Kirim Permintaan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.login-bg {
    background:
        radial-gradient(circle at 15% 20%, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 42%),
        radial-gradient(circle at 85% 82%, rgba(147, 197, 253, 0.3) 0%, rgba(147, 197, 253, 0) 40%),
        linear-gradient(145deg, #ffffff 0%, #eff6ff 48%, #dbeafe 100%);
    background-size: 130% 130%, 130% 130%, 180% 180%;
    background-position: 0% 0%, 100% 100%, 50% 50%;
    animation: gradientFlow 18s ease-in-out infinite alternate;
}

.glow-orb {
    position: absolute;
    border-radius: 9999px;
    filter: blur(70px);
    opacity: 0.55;
    animation: floatOrb 12s ease-in-out infinite;
}

.glow-orb-a {
    width: 240px;
    height: 240px;
    top: -70px;
    left: -40px;
    background: rgba(96, 165, 250, 0.45);
}

.glow-orb-b {
    width: 280px;
    height: 280px;
    right: -80px;
    bottom: -90px;
    background: rgba(59, 130, 246, 0.36);
    animation-delay: -4s;
}

.glow-orb-c {
    width: 180px;
    height: 180px;
    top: 45%;
    left: 52%;
    background: rgba(191, 219, 254, 0.5);
    animation-delay: -7s;
}

.gradient-sheen {
    position: absolute;
    inset: -20%;
    background: linear-gradient(115deg, rgba(255, 255, 255, 0) 30%, rgba(191, 219, 254, 0.22) 48%, rgba(255, 255, 255, 0) 66%);
    transform: translateX(-30%);
    animation: sheenMove 8s linear infinite;
}

@keyframes floatOrb {

    0%,
    100% {
        transform: translate3d(0, 0, 0) scale(1);
    }

    50% {
        transform: translate3d(0, -18px, 0) scale(1.06);
    }
}

@keyframes sheenMove {
    0% {
        transform: translateX(-35%) rotate(0deg);
    }

    100% {
        transform: translateX(35%) rotate(0deg);
    }
}

@keyframes gradientFlow {
    0% {
        background-position: 0% 0%, 100% 100%, 50% 50%;
    }

    50% {
        background-position: 20% 30%, 80% 70%, 40% 60%;
    }

    100% {
        background-position: 35% 45%, 65% 55%, 60% 40%;
    }
}
</style>
