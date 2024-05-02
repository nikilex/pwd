<template>
    <div class="modal" tabindex="-1" id="board-modal" ref="boardModal">
        <div class="modal-dialog">
            <div class="modal-content card__modal__content">
                <div v-if="loadingCard" class="d-flex align-items-center justify-content-center h-100 w-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <template v-else>
                    <div class="modal-header">
                        <h5 v-if="!isEditableTitle" @click="editCardTitle" class="modal-title">
                            {{ card.title }}
                        </h5>

                        <div v-else class="input-group" v-click-outside="closeEditTitle">
                            <input
                                v-model="card.title"
                                type="text"
                                class="form-control"
                                placeholder="Ввести заголовок карточки"
                            />

                            <button
                                @click="saveCardTitle"
                                class="btn btn-outline-primary"
                                type="button"
                                :disabled="loadingSaveTitle || !card.title"
                            >
                                <div
                                    v-if="loadingSaveTitle"
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-9">
                                <div
                                    v-if="!isEditableDescription"
                                    @click="editDescription"
                                    class="card__description border border-secondary p-2 rounded"
                                >
                                    <div class="border" v-html="card.description" />
                                </div>
                                <div v-else class="row" v-click-outside="closeEditDescription">
                                    <div class="col-12">
                                        <ckeditor
                                            v-model="editorText"
                                            @ready="onReady"
                                            @input="onChange"
                                            :editor="editor"
                                        ></ckeditor>
                                        <!-- <textarea
                                    v-model="card.description"
                                    class="form-control"
                                    placeholder="Добавьте описание"
                                    rows="3"
                                ></textarea> -->
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-2">
                                        <button
                                            @click="saveDescription"
                                            class="btn btn-outline-primary"
                                            type="button"
                                            :disabled="loadingSaveDescription"
                                        >
                                            <div
                                                v-if="loadingSaveDescription"
                                                class="spinner-border spinner-border-sm text-primary"
                                                role="status"
                                            >
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <i v-else class="la la-check nav-icon" />
                                        </button>
                                        <button
                                            @click="closeEditDescription"
                                            class="btn btn-outline-danger"
                                            type="button"
                                        >
                                            <i class="la la-close nav-icon" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="row g-0">
                                    <div class="col">
                                        <select
                                            v-model="selectedBoard"
                                            class="form-select form-select-sm mb-2 rounded-2"
                                        >
                                            <option v-for="board in boards" :value="board.id" :key="board.id">
                                                {{ board.title }}
                                            </option>
                                        </select>
                                        <select
                                            v-model="selectedColumn"
                                            class="form-select form-select-sm mb-2 rounded-2"
                                        >
                                            <option v-for="column in columns" :value="column.id" :key="column.id">
                                                {{ column.title }}
                                            </option>
                                        </select>
                                        <button
                                            @click="transferCard"
                                            class="btn btn-outline-primary btn-sm w-100 mb-2 rounded-2"
                                            type="button"
                                        >
                                            <div
                                                v-if="loadingTransferCard"
                                                class="spinner-border spinner-border-sm text-primary"
                                                role="status"
                                            >
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <template v-else>Перенести</template>
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <button
                                            class="btn btn-sm btn-outline-primary w-100 rounded-2"
                                            @click="archiveCard"
                                        >
                                            Архивировать
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import CKEditor from '@ckeditor/ckeditor5-vue'
import CKEditorInspector from '@ckeditor/ckeditor5-inspector'

export default {
    name: 'BoardModal',

    components: {
        ckeditor: CKEditor.component,
    },

    props: {
        boardId: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            card: {
                id: null,
                title: '',
                titleEdit: false,
                description: '',
            },
            editor: ClassicEditor,
            editorText: '',
            isEditableDescription: false,
            loadingSaveDescription: false,
            loadingSaveTitle: false,
            loadingTransferCard: false,
            loadingArchiveCard: false,
            loadingCard: false,
            isEditableTitle: false,
            selectedBoard: null,
            selectedColumn: null,
            boards: [],
            columns: [],
            myModal: null,
        }
    },

    watch: {
        'card.id': {
            handler: function () {
                if (this.card.id) {
                    this.loadCard()
                }
            },
        },

        selectedBoard: {
            handler: function () {
                if (this.card.id) {
                    this.getColumns()
                }
            },
        },
    },

    mounted() {
        const modal = document.getElementById('board-modal')
        document.body.appendChild(modal)

        this.myModal = new bootstrap.Modal(modal, {
            keyboard: false,
        })
    },

    methods: {
        async loadCard() {
            this.loadingCard = true
            await this.getBoards()
            await this.getCard()
            // await this.getColumns()
            this.loadingCard = false
        },

        archiveCard() {
            this.loadingArchiveCard = true

            return axios
                .delete('/archiveCard', {
                    data: {
                        card_id: this.card.id,
                    },
                })
                .then((res) => {
                    this.$emit('cardArchived', this.card)
                    this.closeModal()
                })
                .finally(() => (this.loadingArchiveCard = false))
        },

        onReady(editor) {
            CKEditorInspector.attach(editor)
        },
        onChange(data) {},

        getBoards() {
            return axios.get('/get-boards').then((res) => {
                this.boards = res.data
                this.selectedBoard = this.boards.filter((item) => item.id === Number(this.boardId))[0]?.id
            })
        },

        getColumns() {
            return axios
                .get('/get-columns', {
                    params: {
                        id: this.selectedBoard,
                    },
                })
                .then((res) => {
                    this.columns = res.data
                })
        },

        getCard() {
            return axios
                .get('/api/get-card', {
                    params: {
                        card_id: this.card.id,
                    },
                })
                .then((res) => {
                    this.card = { ...this.card, ...res.data }
                    this.editorText = this.card.description
                })
        },

        transferCard() {
            this.loadingTransferCard = true

            return axios
                .put('/transfer-card', {
                    card_id: this.card.id,
                    column_id: this.selectedColumn,
                })
                .then((res) => {
                    this.$emit('cardTransfered', {
                        data: res.data,
                        oldColumnId: this.card.column_id,
                        newColumnId: this.selectedColumn,
                        card: this.card,
                    })

                    this.loadingTransferCard = false

                    if (this.selectedBoard !== this.boardId) {
                        this.closeModal()
                    }
                })
        },

        changeCardId(cardId) {
            this.card.id = cardId
        },

        closeEditDescription() {
            this.isEditableDescription = false
        },

        closeEditTitle() {
            this.isEditableTitle = false
        },

        editCardTitle() {
            this.isEditableTitle = true
        },

        editDescription() {
            this.isEditableDescription = true
        },

        closeModal() {
            this.myModal.hide()
            //this.myModal.dispose()
        },

        openModal() {
            this.myModal.show()
            // this.loadCard()
        },

        saveDescription() {
            this.loadingSaveDescription = true

            axios
                .put(`/api/save-card-description/${this.card.id}`, { description: this.editorText })
                .then(() => {
                    this.card.description = this.editorText
                    this.isEditableDescription = false
                })
                .finally(() => {
                    this.loadingSaveDescription = false
                })
        },

        saveCardTitle() {
            this.loadingSaveTitle = true

            axios
                .put(`/api/save-card-title/${this.card.id}`, { title: this.card.title })
                .then(() => {
                    this.$emit('titleEdited', this.card)
                    this.isEditableTitle = false
                })
                .finally(() => {
                    this.loadingSaveTitle = false
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
/* don't add "scoped"; note that this will also globalize the CSS for all editors in your project */
.ck-editor__editable {
    min-height: 150px;
}
</style>
