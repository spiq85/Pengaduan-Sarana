<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch, nextTick } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import dayjs from "dayjs";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({
    aspirations: Array,
    categories: Array,
    locations: Array,
});

const page = usePage();
const sidebarOpen = ref(false);
const isComposing = ref(page.url.includes("new=1") || props.aspirations.length === 0);
const currentStep = ref(0);
const previewUrl = ref(null);
const chatContainer = ref(null);

const form = useForm({
    submission_mode: "template",
    title: "",
    id_category: "",
    id_location: "",
    room_number: "",
    custom_location: "",
    custom_context: "",
    description: "",
    image: null,
});

const modeChoices = [
    { key: 'template', title: 'Template', description: 'Ikuti opsi yang disediakan sistem langkah demi langkah.' },
    { key: 'custom', title: 'Custom', description: 'Lokasi bebas dan konteks custom.' },
];

const messages = ref([]);
const isTyping = ref(false);

const scrollToBottom = (smooth = true) => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTo({
                top: chatContainer.value.scrollHeight,
                behavior: smooth ? "smooth" : "auto",
            });
        }
    });
};

const streamText = async (text, messageId) => {
    const index = messages.value.findIndex((m) => m.id === messageId);
    if (index === -1) return;
    messages.value[index].isStreaming = true;
    messages.value[index].text = "";
    for (let i = 0; i < text.length; i++) {
        messages.value[index].text += text[i];
        if (i % 3 === 0) scrollToBottom(false); 
        await new Promise(r => setTimeout(r, 25));
    }
    messages.value[index].isStreaming = false;
    scrollToBottom(true);
};

const addBotMessage = async (text, type = "text", options = null) => {
    isTyping.value = true;
    await new Promise(resolve => setTimeout(resolve, 500 + Math.random() * 300));
    isTyping.value = false;
    const newMessage = { id: Date.now(), sender: "bot", text: "", type, options, isStreaming: true };
    messages.value.push(newMessage);
    await streamText(text, newMessage.id);
};

const addUserMessage = (text, displayValue = null) => {
    messages.value.push({ id: Date.now(), sender: "user", text: displayValue || text });
    scrollToBottom();
};

const steps = computed(() => {
    const mode = form.submission_mode;
    if (mode === 'template') {
        return [
            { key: "mode", bot: "Halo! Saya Asisten Aspirasi. Pilih mode pengiriman aspirasi kamu dulu ya.", type: "mode_select" },
            { key: "title", bot: "Bagus! Sekarang, apa judul atau inti dari aspirasi kamu?", placeholder: "Ketik judul singkat...", validate: (val) => val.trim().length > 3 },
            { key: "category", bot: "Kategori mana yang paling pas buat masalah ini?", type: "category_select" },
            { key: "location", bot: "Di mana lokasinya?", type: "location_select" },
            { key: "description", bot: "Bisa ceritakan detail kejadian atau masalahnya?", placeholder: "Ceritakan selengkapnya...", validate: (val) => val.trim().length > 10 },
            { key: "file", bot: "Ada foto buktinya? Lampirin di sini ya biar makin jelas.", type: "file" },
            { key: "confirm", bot: "Semua data sudah lengkap. Ini rangkuman aspirasi kamu. Siap dikirim?", type: "submit" }
        ];
    } else {
        return [
            { key: "mode", bot: "Halo! Saya Asisten Aspirasi. Pilih mode pengiriman aspirasi kamu dulu ya.", type: "mode_select" },
            { key: "category", bot: "Pilih kategori aspirasi kamu dulu ya.", type: "category_select" },
            { key: "title", bot: "Judulnya apa nih buat aspirasi custom kamu?", placeholder: "Ketik judul...", validate: (val) => val.trim().length > 3 },
            { key: "custom_location", bot: "Lokasinya di mana? Ketik manual yang spesifik ya.", placeholder: "Contoh: Samping Lab Komputer", validate: (val) => val.trim().length > 2 },
            { key: "description", bot: "Detail masalahnya gimana?", placeholder: "Ceritakan selengkapnya...", validate: (val) => val.trim().length > 10 },
            { key: "file", bot: "Ada fotonya?", type: "file" },
            { key: "confirm", bot: "Data custom sudah siap. Cek rangkumannya ya.", type: "submit" }
        ];
    }
});

const handleUserInput = async (value, displayValue = null) => {
    const step = steps.value[currentStep.value];
    if (step.key === 'mode') form.submission_mode = value;
    else if (step.key === 'title') form.title = value;
    else if (step.key === 'category') form.id_category = value;
    else if (step.key === 'location') { form.id_location = value.id_location; form.room_number = value.room_number; }
    else if (step.key === 'custom_location') form.custom_location = value;
    else if (step.key === 'description') form.description = value;
    else if (step.key === 'file') {
        form.image = value;
        if (value instanceof File) previewUrl.value = URL.createObjectURL(value);
    }

    addUserMessage(displayValue || (typeof value === 'string' ? value : 'Dikonfirmasi'));
    if (currentStep.value < steps.value.length - 1) {
        currentStep.value++;
        const nextStepData = steps.value[currentStep.value];
        await addBotMessage(nextStepData.bot, nextStepData.type);
    }
};

onMounted(async () => {
    console.log("Found aspirations:", props.aspirations.length);
    if (isComposing.value && messages.value.length === 0) {
        currentStep.value = 0;
        await addBotMessage(steps.value[0].bot, steps.value[0].type);
    }
});

watch(isComposing, async (newVal) => {
    if (newVal && messages.value.length === 0) {
        currentStep.value = 0;
        await addBotMessage(steps.value[0].bot, steps.value[0].type);
    }
});

const startNewChat = () => {
    const today = dayjs().format('YYYY-MM-DD');
    const hasSubmittedToday = props.aspirations.some(a => {
        return dayjs(a.input_at || a.created_at).format('YYYY-MM-DD') === today;
    });

    if (hasSubmittedToday) {
        Swal.fire({
            title: "LIMIT HARIAN",
            text: "Kamu sudah mengirim aspirasi hari ini. Coba lagi besok ya!",
            icon: "warning",
            confirmButtonColor: "#4f46e5",
        });
        return;
    }

    messages.value = [];
    isComposing.value = true;
    currentStep.value = 0;
    previewUrl.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (form.processing) return;

    // Check limit again right before submitting
    const today = dayjs().format('YYYY-MM-DD');
    const hasSubmittedToday = props.aspirations.some(a => {
        return dayjs(a.input_at || a.created_at).format('YYYY-MM-DD') === today;
    });

    if (hasSubmittedToday) {
        Swal.fire({
            title: "LIMIT TERCAPAI",
            text: "Satu siswa hanya bisa kirim aspirasi sekali dalam sehari. Coba lagi besok ya!",
            icon: "warning",
            confirmButtonColor: "#4f46e5",
        });
        return;
    }
    
    form.post("/student/input-aspirations", {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: "BERHASIL!",
                text: "Aspirasi kamu sudah terkirim.",
                icon: "success",
                confirmButtonColor: "#4f46e5",
            }).then(() => {
                startNewChat();
                isComposing.value = false;
            });
        },
        onError: (errs) => {
            console.error("Submission Errors:", errs);
            const errorMsg = errs.cooldown || Object.values(errs)[0];
            
            Swal.fire({
                title: "GAGAL MENGIRIM",
                text: errorMsg || "Terjadi kesalahan saat memproses data.",
                icon: "error",
                confirmButtonColor: "#f43f5e",
            });
        },
    });
};

const statusTheme = (status) => {
    const s = String(status || "").toLowerCase();
    if (s === "diterima") return "bg-emerald-50 text-emerald-600 border-emerald-100";
    if (s === "ditolak") return "bg-rose-50 text-rose-600 border-rose-100";
    return "bg-amber-50 text-amber-600 border-amber-100";
};

const formatDateTime = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return date.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const handleLogout = () => {
    Swal.fire({
        title: "KELUAR SISTEM?",
        text: "Sesi Anda akan segera diakhiri.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "YA, KELUAR",
        cancelButtonText: "BATAL",
    }).then((result) => {
        if (result.isConfirmed) router.post("/student/logout");
    });
};
</script>

<template>
    <Head title="AI Aspiration Hub" />

    <div class="flex min-h-screen bg-[#fafafa] font-sans selection:bg-blue-100 overflow-hidden">
        
        <!-- Mobile Header -->
        <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-gradient-to-tr from-blue-600 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-robot text-xs"></i>
                </div>
                <span class="font-black text-lg tracking-tighter uppercase italic"><i class="fas fa-robot text-blue-600 me-1"></i>Aspira<span class="text-blue-600">Chat</span></span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-600 active:scale-95 transition-all">
                <i :class="sidebarOpen ? 'fas fa-times' : 'fas fa-bars-staggered'"></i>
            </button>
        </div>

        <div v-if="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 bg-slate-900/40 z-[60] backdrop-blur-sm animate-in fade-in duration-300"></div>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="w-80 bg-white border-r border-slate-100 flex flex-col p-6 fixed lg:sticky top-0 h-screen z-[70] lg:translate-x-0 transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
        >
            <div class="mb-10 px-2 flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-tr from-blue-600 to-violet-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-200 rotate-3">
                    <i class="fas fa-robot text-xl"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 tracking-tighter text-xl italic uppercase leading-none">
                        <i class="fas fa-robot text-blue-600 me-1"></i>Aspira<span class="text-blue-600">Chat</span>
                    </h2>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1">Premium Assistant</p>
                </div>
            </div>

            <button @click="startNewChat" class="mb-8 w-full group flex items-center justify-between bg-slate-900 text-white p-5 rounded-[2rem] hover:bg-blue-600 transition-all duration-500 shadow-xl shadow-slate-900/10 hover:shadow-blue-500/20 active:scale-[0.98]">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                        <i class="fas fa-plus text-[10px]"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">New Chat</span>
                </div>
                <i class="fas fa-arrow-right text-[10px] opacity-30 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all"></i>
            </button>

            <div class="flex-1 flex flex-col min-h-0">
                <p class="px-2 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Riwayat Percakapan</p>
                <div class="space-y-2 overflow-y-auto pr-1 no-scrollbar flex-1 pb-10">
                    <Link
                        v-for="item in aspirations"
                        :key="item.id_input"
                        :href="'/student/input-aspirations/' + item.id_input"
                        class="w-full group text-left rounded-3xl border border-transparent p-4 transition-all duration-500 hover:bg-slate-50 block"
                    >
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <span :class="['text-[8px] font-black uppercase rounded-lg px-2 py-0.5 border', statusTheme(item.submission_status)]">
                                {{ item.submission_status }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400">{{ formatDateTime(item.created_at) }}</span>
                        </div>
                        <p class="text-[13px] font-bold text-slate-700 truncate group-hover:text-blue-600 transition-colors">{{ item.title || 'Tanpa Judul' }}</p>
                        <p class="text-[10px] font-medium text-slate-400 truncate mt-1 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt opacity-40"></i> {{ item.location }}
                        </p>
                    </Link>
                    
                    <div cls
                    v-if="aspirations.length === 0" class="py-12 px-6 text-center border-2 border-dashed border-slate-100 rounded-[2.5rem]">
                        <i class="fas fa-comment-slash text-2xl text-slate-200 mb-3"></i>
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Belum ada riwayat</p>
                    </div>
                </div>
            </div>

            <div class="mt-auto pt-6 border-t border-slate-100 grid grid-cols-2 gap-3">
                <Link href="/student/dashboard" class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl bg-slate-50 text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all group">
                    <i class="fas fa-th-large text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Dash</span>
                </Link>
                <button @click="handleLogout" class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl bg-rose-50 text-rose-500 hover:bg-rose-100 transition-all group">
                    <i class="fas fa-power-off text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Exit</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col relative h-screen">
            <!-- Top Bar -->
            <header class="h-24 hidden lg:flex items-center justify-between px-10 bg-white/80 backdrop-blur-xl border-b border-slate-100/50 z-40 sticky top-0">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fas fa-robot text-xs"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-black text-slate-800 tracking-widest uppercase">Workspace Aspirasi</h1>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">System Operational • Online</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <NotificationBell />
                </div>
            </header>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto no-scrollbar pt-20 lg:pt-0" ref="chatContainer">
                
                <!-- COMPOSING STATE: Premium AI Chat -->
                <div v-if="isComposing" class="flex flex-col items-center justify-start min-h-full py-12 px-6">
                    <div class="w-full max-w-2xl flex-1 flex flex-col gap-10 pb-40">
                        <div class="text-center mb-6 animate-in fade-in slide-in-from-top-4 duration-1000">
                            <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 via-violet-600 to-blue-500 rounded-[2rem] flex items-center justify-center shadow-2xl shadow-blue-500/20 mx-auto mb-6 rotate-3">
                                <i class="fas fa-robot text-white text-2xl"></i>
                            </div>
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Mulai Aspirasi Baru</h2>
                            <p class="text-slate-400 font-medium text-sm">Ceritakan apa yang bisa kami bantu tingkatkan hari ini.</p>
                        </div>

                        <div class="space-y-10 flex flex-col">
                            <TransitionGroup name="chat">
                                <div v-for="msg in messages" :key="msg.id" :class="['flex w-full', msg.sender === 'user' ? 'justify-end' : 'justify-start']">
                                    <div :class="['flex max-w-[85%] gap-4', msg.sender === 'user' ? 'flex-row-reverse' : 'flex-row']">
                                        <div v-if="msg.sender === 'bot'" class="flex-shrink-0 w-8 h-8 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shadow-sm self-end mb-1">
                                            <i class="fas fa-robot text-[10px] text-blue-500"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <div :class="['text-[15px] leading-relaxed font-medium px-5 py-3 shadow-sm min-w-[3rem]', msg.sender === 'user' ? 'bg-slate-900 text-white rounded-[1.8rem] rounded-tr-none' : 'bg-white border border-slate-100 text-slate-800 rounded-[1.8rem] rounded-bl-none']">
                                                {{ msg.text }}
                                                <div v-if="msg.type === 'file' && previewUrl && !msg.isStreaming" class="mt-4 rounded-2xl overflow-hidden border border-slate-100 shadow-xl animate-in zoom-in"><img :src="previewUrl" class="w-full h-auto object-cover max-h-80"></div>
                                                <div v-if="msg.sender === 'bot' && !msg.isStreaming && msg.type === 'mode_select'" class="mt-4 flex flex-col gap-2">
                                                    <button v-for="mode in modeChoices" :key="mode.key" @click="handleUserInput(mode.key, mode.title)" class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-blue-600 hover:text-white transition-all text-left flex justify-between items-center group/btn"><span class="text-[10px] font-black uppercase tracking-widest">{{ mode.title }}</span><i class="fas fa-chevron-right text-[8px] opacity-0 group-hover/btn:opacity-100 transition-all"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </TransitionGroup>
                            <div v-if="isTyping" class="flex justify-start">
                                <div class="flex gap-4">
                                    <div class="w-8 h-8 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center self-end"><i class="fas fa-robot text-[10px] text-blue-400"></i></div>
                                    <div class="flex items-center gap-1.5 px-5 py-3 rounded-[1.8rem] rounded-bl-none bg-white border border-slate-100 shadow-sm"><div class="flex gap-1"><span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce [animation-delay:-0.3s]"></span><span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce [animation-delay:-0.15s]"></span><span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce"></span></div></div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Input Area -->
                        <div v-if="!isTyping && !messages[messages.length-1]?.isStreaming" class="fixed bottom-10 left-1/2 lg:left-[calc(50%+160px)] -translate-x-1/2 w-[calc(100%-48px)] max-w-2xl z-[80] animate-in slide-in-from-bottom-4">
                            <div class="bg-white rounded-[2.5rem] border border-slate-200 p-2 shadow-[0_20px_50px_rgba(0,0,0,0.1)] flex flex-col transition-all duration-500 focus-within:border-blue-400 focus-within:shadow-blue-500/10">
                                <div class="flex items-center gap-2 p-1">
                                    <template v-if="steps[currentStep]?.key === 'mode'"><div class="flex-1 px-6 py-4 text-xs font-bold text-slate-400 italic">Pilih mode di atas...</div></template>
                                    <template v-else-if="['title', 'description', 'custom_location'].includes(steps[currentStep]?.key)">
                                        <textarea v-if="steps[currentStep]?.key === 'description'" v-model="form.description" rows="1" @input="(e) => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" @keydown.enter.exact.prevent="form.description.length > 10 && handleUserInput(form.description)" :placeholder="steps[currentStep]?.placeholder || 'Ketik pesan Anda...'" class="flex-1 bg-transparent border-none focus:ring-0 px-6 py-4 text-[15px] font-medium text-slate-700 resize-none max-h-40 overflow-y-auto no-scrollbar"></textarea>
                                        <input v-else v-model="form[steps[currentStep]?.key]" type="text" @keyup.enter="form[steps[currentStep]?.key].length > 2 && handleUserInput(form[steps[currentStep]?.key])" :placeholder="steps[currentStep]?.placeholder" class="flex-1 bg-transparent border-none focus:ring-0 px-6 py-4 text-[15px] font-medium text-slate-700">
                                        <button @click="handleUserInput(form[steps[currentStep]?.key])" :disabled="isTyping || (steps[currentStep]?.key === 'description' ? form.description.length <= 10 : form[steps[currentStep]?.key].length <= 2)" class="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-blue-600 hover:scale-105 active:scale-95 transition-all shadow-xl disabled:opacity-20"><i class="fas fa-arrow-up"></i></button>
                                    </template>
                                    <template v-else-if="steps[currentStep]?.key === 'category'"><div class="flex-1 p-4 grid grid-cols-2 sm:grid-cols-3 gap-3"><button v-for="cat in categories" :key="cat.id_category" @click="handleUserInput(cat.id_category, cat.category_name)" class="flex flex-col items-center justify-center p-6 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:shadow-xl hover:shadow-blue-200 transition-all group/cat"><span class="text-[10px] font-black uppercase tracking-[0.2em] text-center">{{ cat.category_name }}</span></button></div></template>
                                    <template v-else-if="steps[currentStep]?.key === 'location'"><div class="flex-1 flex flex-col md:flex-row gap-3 p-3"><select v-model="form.id_location" class="flex-1 bg-slate-50 border-slate-100 rounded-2xl text-[10px] font-black uppercase tracking-widest py-3 px-4 focus:ring-2 focus:ring-blue-400"><option value="" disabled>Pilih Lokasi</option><option v-for="loc in locations" :key="loc.id_location" :value="loc.id_location">{{ loc.location_name }}</option></select><input v-if="locations.find(l => String(l.id_location) === String(form.id_location))?.location_type === 'kelas'" v-model="form.room_number" type="text" placeholder="No Ruang" class="w-full md:w-32 bg-slate-50 border-slate-100 rounded-2xl text-[10px] font-black uppercase tracking-widest py-3 px-4 focus:ring-2 focus:ring-blue-400"><button @click="handleUserInput({id_location: form.id_location, room_number: form.room_number}, (locations.find(l => String(l.id_location) === String(form.id_location))?.location_name || 'Lokasi') + (form.room_number ? ` (${form.room_number})` : ''))" :disabled="!form.id_location" class="px-8 py-3 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest active:scale-95 transition-all disabled:opacity-20">Set</button></div></template>
                                    <template v-else-if="steps[currentStep]?.key === 'file'"><div class="flex-1 flex items-center justify-between p-3 pl-8"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm"><i class="fas fa-image text-sm"></i></div><div class="flex flex-col"><p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Langkah 6/7</p><p class="text-xs font-bold text-slate-500">Lampirkan foto bukti (Wajib)</p></div></div><label class="px-10 py-3.5 rounded-full bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] cursor-pointer hover:bg-blue-600 active:scale-95 transition-all shadow-xl shadow-slate-200">UPLOAD FOTO <input type="file" class="hidden" accept="image/*" @change="(e) => handleUserInput(e.target.files[0], 'Foto telah dilampirkan')"></label></div></template>
                                    <template v-else-if="steps[currentStep]?.key === 'confirm'"><div class="flex-1 p-3 flex flex-col gap-5"><div class="bg-slate-50/80 backdrop-blur rounded-[2rem] p-6 border border-slate-100 space-y-4"><div class="flex justify-between items-center pb-3 border-b border-slate-200/50"><span class="text-[9px] font-black text-blue-500 uppercase tracking-widest">Rangkuman Aspirasi</span><i class="fas fa-check-double text-blue-400 text-[10px]"></i></div><div class="space-y-3"><div class="flex justify-between items-center"><span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Judul</span><span class="text-[11px] font-bold text-slate-700">{{ form.title }}</span></div><div class="flex justify-between items-center"><span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kategori</span><span class="text-[11px] font-bold text-slate-700">{{ categories.find(c => String(c.id_category) === String(form.id_category))?.category_name || '-' }}</span></div><div class="flex justify-between items-center"><span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Lokasi</span><span class="text-[11px] font-bold text-slate-700">{{ form.submission_mode === 'template' ? (locations.find(l => String(l.id_location) === String(form.id_location))?.location_name + (form.room_number ? ` ${form.room_number}` : '')) : form.custom_location }}</span></div></div></div><button @click="submit" :disabled="form.processing" class="w-full py-4 rounded-[2rem] bg-gradient-to-r from-blue-600 to-blue-600 text-white text-[10px] font-black uppercase tracking-[0.3em] shadow-2xl shadow-blue-500/30 hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50">{{ form.processing ? 'Memproses...' : 'Kirim Aspirasi Sekarang' }}</button></div></template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EMPTY STATE -->
                <div v-else class="flex flex-col items-center justify-center min-h-full py-20 px-10 text-center">
                    <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mb-8 border border-slate-100 shadow-inner"><i class="fas fa-comment-dots text-4xl text-slate-200"></i></div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-3 italic uppercase">Halo! Ada yang bisa dibantu?</h2>
                    <p class="text-slate-400 max-w-md mx-auto font-medium text-sm mb-8">Klik tombol <b>New Chat</b> untuk mulai membuat aspirasi baru, atau pilih riwayat percakapan di samping.</p>
                    
                    <!-- Stats / Quick Info -->
                    <div class="grid grid-cols-2 gap-4 w-full max-w-sm">
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-center">
                            <p class="text-2xl font-black text-blue-600 mb-1">{{ aspirations.length }}</p>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Aspirasi</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-center">
                            <p class="text-2xl font-black text-emerald-500 mb-1">{{ aspirations.filter(a => a.aspiration?.progress_status === 'Selesai').length }}</p>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Selesai</p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
body { font-family: 'Outfit', sans-serif; background-color: #fafafa; }
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes slide-in-top { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes slide-in-bottom { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes zoom-in { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.animate-in { animation-duration: 0.4s; animation-fill-mode: forwards; animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1); }
.chat-enter-active { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
.chat-enter-from { opacity: 0; transform: translateY(20px); }
.chat-move { transition: transform 0.5s ease; }
.fade-in { animation-name: fade-in; }
.zoom-in { animation-name: zoom-in; }
.slide-in-from-top-4 { animation-name: slide-in-top; }
.slide-in-from-bottom-6 { animation-name: slide-in-bottom; }
input, textarea, select { outline: none !important; }
::selection { background: #e0e7ff; color: #4338ca; }
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
