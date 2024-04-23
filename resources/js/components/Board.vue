<template>
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-auto">
                <h1 class="mb-4">{{ board.title }}</h1>
            </div>
            <div class="col-auto">
                <h3 class="mb-3">
                    <button @click="addColumn" class="btn btn-link text-light">+ Добавить колонку</button>
                </h3>
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
            :board-id="board.id"
            ref="modal"
        />
    </div>
</template>

<script>
import BoardModal from './BoardModal.vue'
import draggable from 'vuedraggable'
import Column from './Column.vue'
import { cloneDeep } from 'lodash'

export default {
    name: 'Board',

    components: {
        BoardModal,
        Column,
        draggable,
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

           this.columns[oldColumnIndex].cards = this.columns[oldColumnIndex].cards.filter((item) => item.id !== val.card.id)
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
                        column.uuid = Symbol("id")

                        column.cards = column.cards.map((card) => {
                            card.uuid = Symbol("id")
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
