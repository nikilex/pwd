<template>
    <div class="container mt-3">
        <div class="row justify-content-between">
            <div class="col-auto">
                <h1 class="mb-4">{{ board.title }}</h1>
            </div>
            <div class="col-auto">
                <div class="row g-0 align-items-center">
                    <div class="col-auto">
                        <h3 class="mb-0">
                            <button @click="addColumn" class="btn btn-link text-light">+ Добавить колонку</button>
                        </h3>
                    </div>
                    <div class="col-auto">
                        <Popper>
                            <button class="btn btn-outline-primary">
                                <i class="las la-ellipsis-h"></i>
                            </button>

                            <template #content>
                                <ul class="list-group bg-light">
                                    <li class="list-group-item p-2">
                                        <span @click="openArchiveModal" class="text-danger c-pointer">Архив</span>
                                    </li>
                                </ul>
                            </template>
                        </Popper>
                    </div>
                </div>
            </div>
        </div>
        <div class="row flex-nowrap gap-3 g-0 overflow-scroll g-0">
            <!-- <div
                v-for="(column, columnIndex) in columns"
                class="col-auto"
                :key="columnIndex"
            > -->
            <draggable
                v-model="columns"
                @change="columnOrderChange"
                @start="columnDraggableStart"
                @end="columnDraggableEnd"
                :component-data="getComponentData()"
                :disabled="columnDragabbleDisabled"
                handle=".column__draggable"
                group="people"
                item-key="id"
            >
                <template #item="{ element }">
                    <!-- <Column
                            @changeCard="changeCard"
                            @removeEmptyColumn="removeEmptyColumn"
                            :board-id="board.id"
                            :column="column"
                        /> -->
                    <div class="col-auto">
                        <Column
                            @changeCard="changeCard"
                            @removeEmptyColumn="removeEmptyColumn"
                            @cardDraggableStart="cardDraggableStart"
                            @cardDraggableEnd="cardDraggableEnd"
                            @localColumnChanged="localColumnChanged"
                            @columnArchived="columnArchived"
                            :board-id="board.id"
                            :column="element"
                            :card-drag-disabled="cardDraggableDisabled"
                        />
                    </div>
                </template>
            </draggable>
            <!-- </div> -->
        </div>

        <BoardModal
            @title-edited="modalTitleEdited"
            @cardTransfered="cardTransfered"
            @cardArchived="cardArchived"
            :board-id="board.id"
            ref="modal"
        />

        <BoardArchiveModal
            @unarchivedColumn="unarchivedColumn"
            @unarchivedCard="unarchivedCard"
            :board-id="board.id"
            ref="modalArchive"
        />
    </div>
</template>

<script>
import BoardModal from './BoardModal.vue'
import BoardArchiveModal from './BoardArchiveModal.vue'
import draggable from 'vuedraggable'
import Column from './Column.vue'
import Popper from 'vue3-popper'

export default {
    name: 'Board',

    components: {
        BoardModal,
        BoardArchiveModal,
        Column,
        draggable,
        Popper,
    },

    props: {
        board: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            columns: [],
            columnTitleEdit: false,
            loadingChangeColumnTitle: false,
            loadingChangeCardTitle: false,
            drag: false,
            activeNames: null,
            columnDragabbleDisabled: false,
            cardDraggableDisabled: false,
        }
    },

    computed: {
        realColumnsMoreThanWithEmptyColumn() {
            if (this.columns) {
                return this.columns.length && this.columns.length > this.columns.filter((item) => item.id).length
            }
        },
    },

    created() {
        if (this.board.id) {
            this.getColumns()
        }
    },

    methods: {
        localColumnChanged(val) {
            let index = this.columns.findIndex((item) => item.uuid === val.uuid)
            this.columns[index] = val
        },

        unarchivedColumn(column) {
            column.uuid = Symbol('id')
            this.columns = [...this.columns, column]
            this.columnOrderChange()
        },

        unarchivedCard(card) {
            card.uuid = Symbol('id')
            let index = this.columns.findIndex((item) => item.id === card.column_id)
            this.columns[index].cards = this.columns[index].cards || []
            this.columns[index].cards = [...this.columns[index].cards, card]
        },

        openArchiveModal() {
            this.$refs.modalArchive.openModal()
        },

        cardArchived(val) {
            let index = this.columns.findIndex((item) => item.id === val.column_id)
            this.columns[index].cards = this.columns[index].cards.filter((item) => item.id !== val.id)
        },

        columnArchived(val) {
            let index = this.columns.findIndex((item) => item.id === val.id)
            this.columns.splice(index, 1)
        },

        cardDraggableStart() {
            this.columnDragabbleDisabled = true
        },

        cardDraggableEnd() {
            this.columnDragabbleDisabled = false
        },

        columnDraggableStart() {
            this.drag = true
            this.cardDraggableDisabled = true
        },

        columnDraggableEnd() {
            this.drag = false
            this.cardDraggableDisabled = false
        },

        columnOrderChange(e) {
            this.columns.forEach((item, index) => {
                item.sort = index
            })

            axios.put('/update-column-sort', {
                columns: this.columns.map((a) => {
                    return { id: a.id, sort: a.sort }
                }),
            })
        },

        getComponentData() {
            return {
                class: 'row col-auto flex-nowrap gap-1 overflow-scroll',
            }
        },

        addColumn() {
            this.columns.push({
                uuid: Symbol('id'),
                title: '',
                titleEdit: true,
                cards: [],
            })
        },

        cardTransfered(val) {
            let newColumnIndex = this.columns.findIndex((item) => item.id === val.newColumnId)

            let oldColumnIndex = this.columns.findIndex((item) => item.id === val.oldColumnId)

            this.columns[newColumnIndex].cards.push(val.card)

            this.columns[oldColumnIndex].cards = this.columns[oldColumnIndex].cards.filter(
                (item) => item.id !== val.card.id,
            )
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
                        id: this.board.id,
                    },
                })
                .then((res) => {
                    this.columns = res.data.map((column) => {
                        column.titleEdit = false
                        column.uuid = Symbol('id')

                        column.cards = column.cards.map((card) => {
                            card.uuid = Symbol('id')
                            card.titleEdit = false
                            return card
                        })

                        return column
                    })
                })
        },

        changeCard(cardId) {
            this.$refs.modal.changeCardId(cardId)
            this.$refs.modal.openModal()
        },

        removeEmptyColumn() {
            this.columns = this.columns.filter((item) => item.id)
        },
    },
}
</script>

<style>
.board-column {
    width: 280px;
}
</style>
