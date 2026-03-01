<script setup>
import { useForm, Link, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({ categories: Array });

const previewUrl = ref(null);

const form = useForm({
    id_category: "",
    location: "",
    description: "",
    image: null,
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post("/student/input-aspirations", {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            previewUrl.value = null;
            Swal.fire({
                icon: "success",
                title: "MANTAP CUY!",
                text: "Aspirasi lu udh masuk sistem.",
                confirmButtonColor: "#2563eb",
            });
        },
        onError: (errors) => {
            if (errors.cooldown) {
                Swal.fire({
                    icon: "warning",
                    title: "SABAR CUY!",
                    text: errors.cooldown,
                    confirmButtonColor: "#0f172a",
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "CEK LAGI!",
                    text: "Ada kolom yang belum diisi bener.",
                    confirmButtonColor: "#be123c",
                });
            }
        },
    });
};
</script>

<template>
    <Head title="Buat Laporan" />
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <Link
                    href="/student/input-aspirations"
                    class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-600 font-bold transition group"
                >
                    <i
                        class="fas fa-arrow-left group-hover:-translate-x-1 transition"
                    ></i>
                    Kembali ke Aspirasi Saya
                </Link>
                <NotificationBell />
            </div>

            <form
                @submit.prevent="submit"
                class="grid grid-cols-1 lg:grid-cols-5 gap-6"
            >
                <div class="lg:col-span-3 space-y-6">
                    <div
                        class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 overflow-hidden"
                    >
                        <div class="bg-slate-900 p-6 text-white">
                            <h2
                                class="text-xl font-black uppercase italic tracking-tighter"
                            >
                                Sampaikan
                                <span class="text-blue-500">Aspirasi</span>
                            </h2>
                        </div>

                        <div class="p-8 space-y-5">
                            <div class="space-y-2">
                                <label
                                    class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 ml-1"
                                    >📂 Kategori</label
                                >
                                <select
                                    v-model="form.id_category"
                                    class="w-full px-5 py-3 rounded-xl border-2 border-slate-100 focus:border-blue-500 outline-none transition-all font-bold bg-slate-50 text-sm"
                                    :class="{
                                        'border-rose-500':
                                            form.errors.id_category,
                                    }"
                                >
                                    <option value="" disabled>
                                        Pilih Kategori...
                                    </option>
                                    <option
                                        v-for="cat in categories"
                                        :key="cat.id_category"
                                        :value="cat.id_category"
                                    >
                                        {{ cat.category_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 ml-1"
                                    >📍 Lokasi</label
                                >
                                <input
                                    v-model="form.location"
                                    type="text"
                                    class="w-full px-5 py-3 rounded-xl border-2 border-slate-100 focus:border-blue-500 outline-none transition-all font-bold text-sm"
                                    :class="{
                                        'border-rose-500': form.errors.location,
                                    }"
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 ml-1"
                                    >📝 Detail</label
                                >
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full px-5 py-3 rounded-xl border-2 border-slate-100 focus:border-blue-500 outline-none transition-all resize-none font-medium text-sm"
                                    :class="{
                                        'border-rose-500':
                                            form.errors.description,
                                    }"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white rounded-[2rem] p-6 shadow-xl shadow-blue-900/5 border border-slate-100"
                    >
                        <label
                            class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 block mb-4"
                            >📸 Foto Bukti</label
                        >
                        <input
                            type="file"
                            @change="handleFileChange"
                            class="hidden"
                            id="photo-add"
                            accept="image/*"
                        />
                        <label
                            for="photo-add"
                            class="cursor-pointer block border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:bg-blue-50 transition relative min-h-[150px] flex items-center justify-center overflow-hidden"
                        >
                            <div v-if="!previewUrl">
                                <i
                                    class="fas fa-camera text-2xl text-slate-300 mb-2"
                                ></i>
                                <p
                                    class="text-[10px] font-bold text-slate-400 uppercase"
                                >
                                    Klik untuk Foto
                                </p>
                            </div>
                            <img
                                v-else
                                :src="previewUrl"
                                class="w-full h-full object-cover rounded-lg shadow-md"
                            />
                        </label>

                        <div
                            v-if="form.errors.cooldown"
                            class="mt-4 p-3 bg-red-50 text-red-600 rounded-lg text-xs font-bold border border-red-100"
                        >
                            {{ form.errors.cooldown }}
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full mt-6 py-4 bg-blue-600 text-white font-black rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all flex items-center justify-center gap-3 uppercase text-sm tracking-widest disabled:opacity-50"
                        >
                            <i
                                v-if="form.processing"
                                class="fas fa-circle-notch animate-spin"
                            ></i>
                            <span v-else
                                >Kirim Laporan
                                <i class="fas fa-paper-plane text-xs"></i
                            ></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
