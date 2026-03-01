import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp, usePage } from "@inertiajs/vue3"; // Tambah usePage
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import "../css/app.css";
import Swal from "sweetalert2"; // Impor SweetAlert
import { watch } from "vue"; // Impor watch

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.mount(el);

        // --- GLOBAL SWEETALERT LOGIC ---
        // Kita monitor properti 'flash' dari Inertia
        watch(
            () => usePage().props.flash,
            (flash) => {
                if (flash && flash.success) {
                    Swal.fire({
                        title: "BERHASIL!",
                        text: flash.success,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                        background: "#1e293b",
                        color: "#fff",
                        iconColor: "#3b82f6",
                        customClass: {
                            popup: "rounded-[2rem] border border-slate-700 shadow-2xl",
                        },
                    });
                }

                if (flash && flash.error) {
                    Swal.fire({
                        title: "WADUH!",
                        text: flash.error,
                        icon: "error",
                        background: "#1e293b",
                        color: "#fff",
                        confirmButtonColor: "#e11d48",
                        confirmButtonText: "OKE CUY",
                        customClass: {
                            popup: "rounded-[2rem] border border-slate-700 shadow-2xl",
                        },
                    });
                }

                // Khusus untuk toggle vote atau pesan ringan
                if (flash && flash.message) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: "#1e293b",
                        color: "#fff",
                    });
                    Toast.fire({
                        icon: "info",
                        title: flash.message,
                    });
                }
            },
            { deep: true },
        );
    },
});
