<template>
    <div class="modal" tabindex="-1" id="board-modal" ref="boardModal">
        <div class="modal-dialog">
            <div class="modal-content card__modal__content">
                <div v-if="loadingArchive" class="d-flex align-items-center justify-content-center h-100 w-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <template v-else>
                    <div class="modal-header">
                        <h5 class="modal-title">Архив</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a
                                    @click="setActiveTab('columns')"
                                    class="nav-link c-pointer"
                                    :class="{ active: activeTab === 'columns' }"
                                    >Списки</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    @click="setActiveTab('cards')"
                                    class="nav-link c-pointer"
                                    :class="{ active: activeTab === 'cards' }"
                                    >Карточки</a
                                >
                            </li>
                        </ul>
                        <div class="row">
                            <div v-if="activeTab === 'columns'" class="col">
                                <ul class="list-group list-group-flush">
                                    <li v-for="column in columns" :key="column.id" class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>{{ column.title }}</div>
                                            <div>
                                                <button
                                                    @click="unarchiveColumn(column)"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <div
                                                        v-if="unarchiveColumnLoading"
                                                        class="spinner-border spinner-border-sm text-primary"
                                                        role="status"
                                                    >
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <template v-else>Вернуть на доску</template>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-if="activeTab === 'cards'" class="col">
                                <ul class="list-group list-group-flush">
                                    <li v-for="card in cards" :key="card.id" class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>{{ card.title }}</div>
                                            <div>
                                                <button
                                                    @click="unarchiveCard(card)"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <div
                                                        v-if="unarchiveCardLoading"
                                                        class="spinner-border spinner-border-sm text-primary"
                                                        role="status"
                                                    >
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <template v-else>Вернуть на доску</template>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'BoardArchiveModal',

    props: {
        boardId: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            columns: [],
            cards: [],
            activeTab: 'columns',
            myModal: null,
            loadingArchive: false,
            unarchiveColumnLoading: false,
            unarchiveCardLoading: false,
        }
    },

    mounted() {
        const modal = document.getElementById('board-modal')
        document.body.appendChild(modal)

        this.myModal = new bootstrap.Modal(modal, {
            keyboard: false,
        })

        modal.addEventListener('shown.bs.modal', () => {
            this.getAchive()
        })
    },

    methods: {
        getAchive() {
            this.getArchivedColumns()
            this.getArchivedCards()
        },

        closeModal() {
            this.myModal.hide()
            this.myModal.dispose()
        },

        openModal() {
            this.myModal.show()
        },

        setActiveTab(tab) {
            this.activeTab = tab
        },

        getArchivedColumns() {
            return axios
                .get('/get-archived-columns', {
                    params: {
                        board_id: this.boardId,
                    },
                })
                .then((res) => {
                    this.columns = res.data
                })
        },

        getArchivedCards() {
            return axios
                .get('/get-archived-cards', {
                    params: {
                        board_id: this.boardId,
                    },
                })
                .then((res) => {
                    this.cards = res.data
                })
        },

        unarchiveColumn(column) {
            this.unarchiveColumnLoading = true

            return axios
                .put('/unarchived-column', {
                    column_id: column.id,
                })
                .then((res) => {
                    this.$emit('unarchivedColumn', column)
                    this.columns = this.columns.filter((item) => item.id !== column.id)
                    this.getArchivedCards()
                })
                .finally(() => (this.unarchiveColumnLoading = false))
        },

        unarchiveCard(card) {
            this.unarchiveCardLoading = true

            return axios
                .put('/unarchived-card', {
                    card_id: card.id,
                })
                .then((res) => {
                    this.cards = this.cards.filter((item) => item.id !== card.id)
                    this.$emit('unarchivedCard', card)
                })
                .finally(() => (this.unarchiveCardLoading = false))
        },
    },
}
</script>
