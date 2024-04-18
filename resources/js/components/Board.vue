<template>
    <div class="container">
        <div class="row flex-nowrap gap-3 g-0 overflow-scroll">
            <div
                v-for="(column, columnIndex) in columns"
                class="col-auto"
                :key="columnIndex"
            >
            <!-- <draggable
                @change="log"
                :list="columns"
                handle=".column__draggable"
                group="people"
                item-key="id"
                :component-data="getComponentData()"
                @start="drag = true"
                @end="drag = false"
            >
                <template #item="{ element }"> -->
                    <!-- <div class="col-auto"> -->
                        <Column
                            @changeCard="changeCard"
                            @removeEmptyColumn="removeEmptyColumn"
                            :board-id="boardId"
                            :column="column"
                        />
                    <!-- </div> -->
                <!-- </template>
            </draggable> -->
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
        if (this.boardId) {
            this.getColumns()
        }
    },

    methods: {
        log(e) {
            console.log(e)
        },

        handleChange() {
            console.log('changed')
        },
        inputChanged(value) {
            colnsole.log(value)
            this.activeNames = value
        },
        getComponentData() {
            return {
                onChange: this.handleChange,
                onInput: this.inputChanged,
                class: 'row col-auto flex-nowrap gap-3 overflow-scroll',
                value: this.activeNames,
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
                        id: this.boardId,
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
