<template>
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-auto">
                <h1 class="mb-4">{{ board.title }}</h1>
            </div>
            <div class="col-auto">
                <h3 v-if="!realColumnsMoreThanWithEmptyColumn" class="mb-3">
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
                handle=".column__draggable"
                group="people"
                item-key="id"
                :component-data="getComponentData()"
                @start="drag = true"
                @end="drag = false"
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
                            :board-id="board.id"
                            :column="element"
                        />
                    </div>
                </template>
            </draggable>
            <!-- </div> -->
        </div>

        <BoardModal @title-edited="modalTitleEdited" @cardTransfered="cardTransfered" :board-id="board.id" ref="modal" />
    </div>
</template>

<script>
import BoardModal from './BoardModal.vue'
import draggable from 'vuedraggable'
import Column from './Column.vue'

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
        }
    },

    computed: {
        realColumnsMoreThanWithEmptyColumn() {
            if (this.columns) {
                return this.columns.length > this.columns.filter((item) => item.id).length
            }
        },
    },

    created() {
        if (this.board.id) {
            this.getColumns()
        }
    },

    methods: {
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
                class: 'row col-auto flex-nowrap gap-3 overflow-scroll',
            }
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

                        column.cards = column.cards.map((card) => {
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
