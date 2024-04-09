<template>
    <div class="container">
        <div class="row flex-nowrap gap-3 overflow-scroll">
            <div
                v-for="(column, columnIndex) in columns"
                class="col-auto board-column h-100 bg-secondary p-3 rounded"
                :key="columnIndex"
            >
                <div class="col">
                    <div class="column__header mb-3">
                        <h3 v-if="!column.titleEdit" @click="editTitle(columnIndex)">
                            {{ column.title }}
                        </h3>
                        <div v-else class="input-group" v-click-outside="outsideTitle">
                            <input
                                v-model="column.title"
                                type="text"
                                class="form-control"
                                placeholder="Ввести заголовок списка"
                            />

                            <button
                                @click="changeColumnTitle(columnIndex)"
                                class="btn btn-outline-primary"
                                type="button"
                                :disabled="loadingChangeColumnTitle || !column.title"
                            >
                                <div
                                    v-if="loadingChangeColumnTitle"
                                    class="spinner-border spinner-border-sm text-primary"
                                    role="status"
                                >
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <i v-else class="la la-check nav-icon" />
                            </button>
                            <button @click="outsideTitle" class="btn btn-outline-danger" type="button">
                                <i class="la la-close nav-icon" />
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="cards-wrapper">
                                <div class="row gap-1">
                                    <div v-for="(card, cardIndex) in column.cards" class="col-12" :key="cardIndex">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div
                                                    v-if="!card.titleEdit"
                                                    class="d-flex justify-content-between align-items-center"
                                                >
                                                    <div>
                                                        <h5
                                                            @click="editCardTitle(columnIndex, cardIndex)"
                                                            class="card-title mb-0"
                                                        >
                                                            {{ card.title }}
                                                        </h5>
                                                    </div>

                                                    <div
                                                        @click="changeCard(card.id)"
                                                        class="text-primary"
                                                        role="button"
                                                    >
                                                        <i class="la la-pen" />
                                                    </div>
                                                </div>
                                                <div v-else class="input-group" v-click-outside="outsideCardTitle">
                                                    <input
                                                        v-model="card.title"
                                                        type="text"
                                                        class="form-control"
                                                        placeholder="Ввести заголовок карточки"
                                                    />

                                                    <button
                                                        @click="changeCardTitle(columnIndex, cardIndex)"
                                                        class="btn btn-outline-primary"
                                                        type="button"
                                                        :disabled="loadingChangeCardTitle || !card.title"
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
                                                    <button
                                                        @click="outsideCardTitle"
                                                        class="btn btn-outline-danger"
                                                        type="button"
                                                    >
                                                        <i class="la la-close nav-icon" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button @click="addCard(column.id)" class="btn btn-link text-light">
                                + Добавить карточку
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" v-if="!realColumnsMoreThanWithEmptyColumn">
                <h3 class="mb-3">
                    <button @click="addColumn" class="btn btn-link text-light">+ Добавить колонку</button>
                </h3>
            </div>
        </div>

        <BoardModal @title-edited="modalTitleEdited" @cardTransfered="cardTransfered" :board-id="boardId" ref="modal" />
    </div>
</template>

<script>
import BoardModal from './BoardModal.vue'

export default {
    name: 'Board',

    components: {
        BoardModal,
    },

    props: {
        boardId: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            columns: [],
            columnTitleEdit: false,
            loadingChangeColumnTitle: false,
            loadingChangeCardTitle: false,
        }
    },

    computed: {
        realColumnsMoreThanWithEmptyColumn() {
            return this.columns.length > this.columns.filter((item) => item.id).length
        },
    },

    mounted() {
        if (this.boardId) {
            this.getColumns()
            this.addTitleEdit()
            this.addCardTitleEdit()
        }
    },

    methods: {
        addCard(columnId) {
            this.columns
                .find((item) => item.id === columnId)
                .cards.push({
                    title: '',
                    titleEdit: true,
                })
        },

        addColumn() {
            this.columns.push({
                title: '',
                titleEdit: true,
                cards: [],
            })
        },

        cardTransfered() {
            this.getColumns()
            this.addTitleEdit()
            this.addCardTitleEdit()
        },

        modalTitleEdited(newCard) {
            this.columns.forEach((item) => {
                item.cards.forEach((item) => {
                    if (item.id === newCard.id) {
                        item.title = newCard.title
                    }
                })
            })
        },

        getColumns() {
            axios
                .get(`/get-columns`, {
                    params: {
                        id: this.boardId,
                    },
                })
                .then((res) => {
                    this.columns = res.data
                })
        },

        editTitle(columnIndex) {
            this.columns[columnIndex].titleEdit = true
        },

        editCardTitle(columnIndex, cardIndex) {
            this.columns[columnIndex].cards[cardIndex].titleEdit = true
        },

        changeCard(cardId) {
            this.$refs.modal.changeCardId(cardId)
            this.$refs.modal.openModal()
        },

        addTitleEdit() {
            this.columns.forEach((item) => {
                item.titleEdit = false
            })
        },

        addCardTitleEdit() {
            this.columns.forEach((item) => {
                item.cards.forEach((item) => {
                    item.titleEdit = false
                })
            })
        },

        removeEmptyColumn() {
            this.columns = this.columns.filter((item) => item.id)
        },

        removeEmptyCards() {
            this.columns = this.columns.forEach((item) => {
                item.cards = item.cards.filter((itemCard) => itemCard.id)
                return item
            })
        },

        outsideTitle(event) {
            this.addTitleEdit()
            this.removeEmptyColumn()
        },

        outsideCardTitle(event) {
            this.addCardTitleEdit()
            this.removeEmptyCards()
        },

        changeColumnTitle(columnIndex) {
            this.loadingChangeColumnTitle = true

            axios
                .post('/api/change-column-title', {
                    columns: this.columns[columnIndex],
                    board_id: this.boardId,
                })
                .then((res) => {
                    this.columns[columnIndex].id = res.data.id
                    this.columns[columnIndex].title = res.data.title
                    this.columns[columnIndex].titleEdit = false
                })
                .finally(() => {
                    this.loadingChangeColumnTitle = false
                })
        },

        changeCardTitle(columnIndex, cardIndex) {
            this.loadingChangeCardTitle = true

            axios
                .post('/api/change-card-title', {
                    column_id: this.columns[columnIndex].id,
                    card: this.columns[columnIndex].cards[cardIndex],
                })
                .then((res) => {
                    this.columns[columnIndex].cards[cardIndex].id = res.data.id
                    this.columns[columnIndex].cards[cardIndex].title = res.data.title
                    this.columns[columnIndex].cards[cardIndex].titleEdit = false
                })
                .finally(() => {
                    this.loadingChangeCardTitle = false
                })
        },
    },
}
</script>

<style>
.board-column {
    width: 280px
}
</style>
