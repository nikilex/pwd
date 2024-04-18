<template>
    <div class="card mb-2">
        <div class="card-body p-2 d-flex align-items-center">
            <div class="card__draggable me-1" style="cursor: pointer">
                <i class="las la-bars"></i>
            </div>
            <div class="card__content w-100">
                <div v-if="!localCard.titleEdit" class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 @click="localCard.titleEdit = true" class="card-title mb-0">
                            {{ localCard.title }}
                        </h5>
                    </div>

                    <div @click="changeCard(localCard.id)" class="text-primary" role="button">
                        <i class="la la-pen" />
                    </div>
                </div>
                <div v-else class="input-group" v-click-outside="outsideCardTitle">
                    <input
                        v-model="localCard.title"
                        type="text"
                        class="form-control"
                        placeholder="Ввести заголовок карточки"
                    />

                    <button
                        @click="changeCardTitle(columnId, localCard)"
                        class="btn btn-outline-primary"
                        type="button"
                        :disabled="loadingChangeCardTitle || !localCard.title"
                    >
                        <div
                            v-if="loadingChangeCardTitle"
                            class="spinner-border spinner-border-sm text-primary"
                            role="status"
                        >
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <i v-else class="la la-check nav-icon" />
                    </button>
                    <button @click="outsideCardTitle" class="btn btn-outline-danger" type="button">
                        <i class="la la-close nav-icon" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { cloneDeep } from 'lodash'

export default {
    name: 'Card',

    props: {
        card: {
            type: Object,
            required: true,
        },

        columnId: {
            typr: String,
            required: true,
        }
    },

    data() {
        return {
            localCard: {
                id: null,
                title: null,
                titleEdit: false,
                column_id: null,
            },
            loadingChangeCardTitle: false,
        }
    },

    created() {
        this.localCard = {...this.localCard, ...cloneDeep(this.card)}
    },

    methods: {
        changeCard(cardId) {
            this.$emit('changeCard', cardId)
        },

        addCardTitleEdit() {
            this.localCard.titleEdit = false
        },

        removeEmptyCards() {
            this.$emit('removeEmptyCards')
        },

        outsideCardTitle() {
            this.addCardTitleEdit()
            this.removeEmptyCards()
        },

        changeCardTitle(columnId, card) {
            this.loadingChangeCardTitle = true

            axios
                .post('/api/change-card-title', {
                    column_id: columnId,
                    card: card,
                })
                .then((res) => {
                    this.localCard.id = res.data.id
                    this.localCard.title = res.data.title
                    this.localCard.titleEdit = false
                })
                .finally(() => {
                    this.loadingChangeCardTitle = false
                })
        },
    },
}
</script>
