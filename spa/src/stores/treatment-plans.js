import { defineStore } from "pinia";

export const useTreatmentPlansStore = defineStore("treatment-plans", {
    state: () => ({
        items: [],
        fetching: true,
        pagiantor: { totalRecords: 0 },
        filters: {},
    }),
    actions: {
        async index() {
            this.items = [];
            this.fetching = true;
            const { page = 1, rows = 10 } = this.pagiantor;

            const { statusText, data } = await this.axios.get(
                "/treatment-plans",
                {
                    params: { page, rows, ...this.filters },
                }
            );
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.pagiantor = data.pagiantor;
            }
        },
        async store(form) {
            const res = await this.axios.post("/treatment-plans", form);

            if (res.statusText === "OK") this.items.unshift(res.data);

            return res;
        },
        async update(id, form) {
            const res = await this.axios.patch(`/treatment-plans/${id}`, form);

            if (res.statusText === "OK") {
                const index = this.items.findIndex((item) => item.id === id);
                this.items[index] = res.data;
            }

            return res;
        },
        async destroy(id) {
            const res = await this.axios.delete(`/treatment-plans/${id}`);

            if (res.statusText === "OK") {
                const index = this.items.findIndex((item) => item.id === id);
                this.items.splice(index, 1);
            }

            return res;
        },
        paginate({ rows, page }) {
            this.pagiantor.rows = rows;
            this.pagiantor.page = page + 1;
            return this.index();
        },
    },
});
