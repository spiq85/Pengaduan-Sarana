<script setup>
import { useForm, Link, Head } from "@inertiajs/vue3";
import { ref } from "vue";

// Terima data dari Controller
const props = defineProps({
    aspiration: Object,
    categories: Array
});

const previewUrl = ref(props.aspiration.image ? `/storage/${props.aspiration.image}` : null);

const form = useForm({
    id_category: props.aspiration.id_category,
    location: props.aspiration.location,
    description: props.aspiration.description,
    image: null, // Default null, diisi kalau mau ganti foto
    _method: 'PUT' // Penting: Laravel butuh spoofing method buat update data dengan file
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    // Karena kirim file, kita tetep pake POST tapi di-spoof jadi PUT di dalam form
    form.post(`/student/input-aspirations/${props.aspiration.id_input}`, {
        forceFormData: true,
        onSuccess: () => {
            // Opsional: Kasih notif atau biarkan redirect dari controller bekerja
        },
    });
};
</script>

<template>
    <Head title="Edit Aspirasi" />
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
        <div class="max-w-5xl mx-auto">
            <Link href="/student/input-aspirations" class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-600 font-bold mb-6 transition group">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition"></i> Kembali ke Riwayat
            </Link>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 overflow-hidden">
                        <div class="bg-slate-900 p-6 text-white">
                            <h2 class="text-xl font-black uppercase italic tracking-tighter">Edit <span class="text-blue-500">Aspirasi</span></h2>
                            <p class="text-[10px] text-slate-400 font-bold">ID LAPORAN: #{{ aspiration.id_input }}</p>
                        </div>
                        
                        <div class="p-8 space-y-5">
                            <div class="space-y-2">
                                <label class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 ml-1">📂 Kategori</label>
                                <select v-model="form.id_category" class="w-full px-5 py-3 rounded-xl border-2 border-slate-100 focus:border-blue-500 outline-none transition-all font-bold bg-slate-50 text-sm" required>
                                    <option v-for="cat in categories" :key="cat.id_category" :value="cat.id_category">
                                        {{ cat.category_name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.id_category" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.id_category }}</div>
                            </div>

                            <div class="space-y-2">
                                <label class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 ml-1">📍 Lokasi</label>
                                <input v-model="form.location" type="text" class="w-full px-5 py-3 rounded-xl border-2 border-slate-100 focus:border-blue-500 outline-none transition-all font-bold text-sm" required />
                            </div>

                            <div class="space-y-2">
                                <label class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 ml-1">📝 Detail Perubahan</label>
                                <textarea v-model="form.description" rows="4" class="w-full px-5 py-3 rounded-xl border-2 border-slate-100 focus:border-blue-500 outline-none transition-all resize-none font-medium text-sm" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-blue-900/5 border border-slate-100">
                        <label class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 block mb-4">📸 Ganti Foto (Opsional)</label>
                        
                        <input type="file" @change="handleFileChange" class="hidden" id="photo-edit" accept="image/*" />
                        <label for="photo-edit" class="cursor-pointer block border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:bg-blue-50 transition relative min-h-[180px] flex items-center justify-center overflow-hidden">
                            <img v-if="previewUrl" :src="previewUrl" class="absolute inset-0 w-full h-full object-cover opacity-20" />
                            <div class="relative z-10 text-center">
                                <i class="fas fa-camera text-2xl text-blue-500 mb-2"></i>
                                <p class="text-[10px] font-bold text-slate-600 uppercase">Klik untuk Ubah Foto</p>
                            </div>
                            <img v-if="previewUrl" :src="previewUrl" class="relative z-20 max-h-32 rounded-lg shadow-lg border-2 border-white" />
                        </label>

                        <button type="submit" :disabled="form.processing" class="w-full mt-6 py-4 bg-blue-600 text-white font-black rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all flex items-center justify-center gap-3 uppercase text-sm tracking-widest disabled:opacity-50">
                            <span v-if="form.processing" class="animate-spin">🌀</span>
                            <span v-else>Simpan Perubahan <i class="fas fa-check-circle text-xs"></i></span>
                        </button>

                        <p class="text-[9px] text-center text-slate-400 mt-4 font-bold uppercase tracking-tighter">Hanya bisa diedit selama status masih "Menunggu"</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>