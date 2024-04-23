<template>
    <div class="column board-column h-auto bg-secondary p-3">
        <div class="column__wrapper">
            <div class="row g-0 flex-nowrap mb-2">
                <div class="col-auto column__draggable me-1" style="cursor: pointer">
                    <i class="las la-bars"></i>
                </div>
                <div class="col column__header">
                    <h3 class="mb-0" v-if="!localColumn.titleEdit" @click="editTitle()">
                        {{ localColumn.title }}
                    </h3>
                    <div v-else class="input-group" v-click-outside="outsideTitle">
                        <input
                            v-model="localColumn.title"
                            type="text"
                            class="form-control"
                            placeholder="Ввести заголовок списка"
                        />

                        <button
                            @click="changeColumnTitle()"
                            class="btn btn-outline-primary p-2 me-1"
                            type="button"
                            :disabled="loadingChangeColumnTitle || !localColumn.title"
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
                        <button @click="outsideTitle" class="btn btn-outline-danger p-2" type="button">
                            <i class="la la-close nav-icon" />
                        </button>
                    </div>
                </div>
            </div>
            <div class="row column__cards-wrapper">
                <div class="col-12">
                    <div class="cards-wrapper">
                        <div class="row gap-1">
                            <draggable
                                @change="cardOrderChange($event, localColumn.id)"
                                @start="cardDraggableStart"
                                @end="cardDraggableEnd"
                                :list="localColumn.cards"
                                :disabled="cardDragDisabled"
                                handle=".card__draggable"
                                group="people"
                                item-key="id"
                            >
                                <template #item="{ element }">
                                    <Card
                                        @changeCard="changeCard"
                                        @removeEmptyCards="removeEmptyCards"
                                        @localCardChanged="localCardChanged"
                                        :card="element"
                                        :column-id="localColumn.id"
                                    />
                                </template>
                            </draggable>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <button @click="addCard(localColumn.id)" class="btn btn-link text-light">
                        + Добавить карточку
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import BoardModal from './BoardModal.vue'
import draggable from 'vuedraggable'
import Card from './cards/Card.vue'
import { cloneDeep } from 'lodash'

export default {
    name: 'Board',

    components: {
        BoardModal,
        Card,
        draggable,
    },

    props: {
        boardId: {
            type: String,
            required: true,
        },

        column: {
            type: Object,
            required: true,
        },

        cardDragDisabled: {
            type: Boolean,
            required: false,
            default: false,
        }
    },

    watch: {
        localColumn: {
            handler: function (value) {
                this.$emit('localColumnChanged', value)
            },
            deep: true,
        },
    },

    data() {
        return {
            localColumn: {
                id: null,
                title: null,
                titleEdit: false,
            },
            columnTitleEdit: false,
            loadingChangeColumnTitle: false,
            drag: false,
        }
    },

    created() {
        if (this.boardId) {
            this.localColumn = { ...this.localColumn, ...cloneDeep(this.column) }
        }
    },

    methods: {
        localCardChanged(val) {
            let index = this.localColumn.cards.findIndex((item) => item.uuid === val.uuid)
            this.localColumn.cards[index] = val
        },

        cardDraggableStart() {
            this.drag = true
            this.$emit('cardDraggableStart')
        },

        cardDraggableEnd() {
            this.drag = false
            this.$emit('cardDraggableEnd')
        },

        cardOrderChange(e, columnId) {
            this.localColumn.cards.forEach((item, index) => {
                item.sort = index
                item.column_id = columnId
            })

            axios.put('/update-card-sort', {
                cards: this.localColumn.cards.map((a) => {
                    return { id: a.id, sort: a.sort, column_id: a.column_id }
                }),
            })
        },

        addCard() {
            this.localColumn.cards.push({
                uuid: Symbol('id'),
                title: '',
                titleEdit: true,
            })
        },

        editTitle() {
            this.localColumn.titleEdit = true
        },

        changeCard(cardId) {
            this.$emit('changeCard', cardId)
        },

        addTitleEdit() {
            this.localColumn.titleEdit = false
        },

        removeEmptyColumn() {
            this.$emit('removeEmptyColumn')
        },

        removeEmptyCards() {
            this.localColumn.cards = this.localColumn.cards.filter((itemCard) => itemCard.id)
        },

        outsideTitle() {
            this.addTitleEdit()
            this.removeEmptyColumn()
        },

        changeColumnTitle() {
            this.loadingChangeColumnTitle = true

            axios
                .post('/api/change-column-title', {
                    column: this.localColumn,
                    board_id: this.boardId,
                })
                .then((res) => {
                    this.localColumn.id = res.data.id
                    this.localColumn.title = res.data.title
                    this.localColumn.titleEdit = false
                })
                .finally(() => {
                    this.loadingChangeColumnTitle = false
                })
        },
    },
}
</script>
