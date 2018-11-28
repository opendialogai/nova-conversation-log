<template>
    <div class="flex flex-wrap -mx-3 mb-3">
        <div class="px-3 mb-6 w-2/3">
            <card class="p-4">
                <div class="clearfix mb-6">
                    <heading class="float-left">Conversation Log</heading>

                    <div class="show-extra-info float-right">
                        Show extra information:
                        <toggle-button v-model="showExtraInfo" :labels="{checked: 'on', unchecked: 'off'}" />
                    </div>
                </div>

                <div class="clearfix mb-6" v-if="userInHandOverMode">
                    <div @click="openLiveChat" id="open" class="live-chat">
                        Open Live Chat
                    </div>
                </div>
                <div class="clearfix mb-6" v-else>
                    <div id="closed" class="live-chat">
                        User Not Online
                    </div>
                </div>

                <div class="clearfix mb-4">
                    <div class="float-left"><b>Bot</b></div>
                    <div class="float-right"><b>User</b></div>
                </div>

                <div class="messages" v-on:scroll="onMessagesScroll">
                    <div class="message mb-6" v-for="(message, index) in messages">
                        <template v-if="showExtraInfo">
                            <template v-if="messages[index-1]">
                                <div class="text-center mb-3">
                                    <div v-if="messages[index-1].conversation_id != message.conversation_id" class="text-70">Conversation: {{ message.conversation_id }}</div>
                                    <div v-if="messages[index-1].scene_id != message.scene_id" class="text-70">Scene: {{ message.scene_id }}</div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="text-center mb-2">
                                    <div class="text-70">Conversation: {{ message.conversation_id }}</div>
                                    <div class="text-70">Scene: {{ message.scene_id }}</div>
                                </div>
                            </template>
                        </template>

                        <template v-if="message.author == 'them'">
                            <div class="text-left them">
                                <div class="user-name" v-if="message.user && message.user.name">{{message.user.name}}</div>
                                <template v-if="message.type == 'image'">
                                    <div class="text">
                                        <template v-if="message.data.img_link">
                                            <a :href="message.data.img_link"><img :src="message.data.img_src" /></a>
                                        </template>
                                        <template v-else>
                                            <img :src="message.data.img_src" />
                                        </template>
                                    </div>
                                </template>
                                <template v-else-if="message.type == 'action'">
                                    <div class="text">
                                        <div>{{ message.message }}</div>
                                        <button v-for="button in message.data.buttons" class="btn btn-default btn-primary mt-1">{{button.text}}</button>
                                    </div>
                                </template>
                                <template v-else-if="message.type == 'list'">
                                    <div class="text">
                                        <div class="list-element" v-for="element in message.data.elements">
                                            <template v-if="element.image">
                                                <div class="list-image float-right mb-1 ml-2"><img :src="element.image" /></div>
                                            </template>

                                            <div class="list-title font-bold mb-1">{{ element.title }}</div>
                                            <div class="list-subtitle mb-1">{{ element.subtitle }}</div>

                                            <div class="list-button">
                                                <template v-if="element.button.url">
                                                    <a class="btn btn-default btn-primary mt-1" :href="element.button.url" :target="element.button.link_new_tab ? '_blank' : '_parent'">{{ element.button.text }}</a>
                                                </template>
                                                <template v-else-if="element.button.callback">
                                                    <button class="btn btn-default btn-primary mt-1">{{ element.button.text }}</button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="text">{{ message.message }}</div>
                                </template>

                                <div class="time text-xs text-70 mt-1">{{ formatDate(message.created_at) }}</div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="text-right me">
                                <div class="text mb-1">{{ message.message }}</div>
                                <div class="time text-xs text-70 mt-1">{{ formatDate(message.created_at) }}</div>
                                <div v-if="showExtraInfo" class="text-70 mt-1">
                                    Matched "{{ message.matched_intent }}" Intent
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="loading-indicator" v-if="loading">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </card>
        </div>

        <div class="px-3 mb-6 w-1/3">
            <card class="p-4">
                <heading class="mb-6">Context Log</heading>

                <div class="context-log">
                    <div v-for="row in contextLogs">
                        <div class="mb-2">{{ row.created_at }}: {{ row.data_type }}: {{ row.value }}</div>
                    </div>
                </div>
            </card>
        </div>
    </div>
</template>

<script>
    import format from "date-fns/format";
    import parse from "date-fns/parse";

    import ToggleButton from "vue-js-toggle-button/src/Button";

    export default {
    components: {
        ToggleButton
    },
    data() {
        return {
            showExtraInfo: false,
            loading: false,
            messagesOffset: 0,
            messages: [],
            contextLogs: [],
            userInHandOverMode: false,
            userId: null
        };
    },
    mounted() {
        this.userId = this.$route.params.user;
        this.fetchMessages(this.$route.params.user, 0);
        this.fetchContextLog(this.$route.params.user);
        this.isUserInHandOverMode(this.$route.params.user)
    },
    methods: {
        fetchContextLog(user) {
            window.axios
                .get(`/admin/conversation-log/context-log/${user}`)
                .then(response => {
                    response.data.forEach(row => {
                        this.contextLogs.push(row);
                    });
                });
        },
        fetchMessages(user, offset) {
            this.loading = true;

            window.axios
                .get(`/admin/conversation-log/conversation-log/${user}/${offset}`)
                .then(response => {
                    this.loading = false;

                    response.data.forEach(message => {
                        if (message.author !== this.userId) {
                            message.author = 'them';
                        }
                        console.log(message.user);
                        this.messages.push(message);
                    });
                });
        },
        onMessagesScroll(e)
        {
            if (!this.loading && (this.messages.length == this.messagesOffset + 100)) {
                if (e.target.offsetHeight + e.target.scrollTop == e.target.scrollHeight) {
                    this.messagesOffset = this.messagesOffset + 100;

                    this.fetchMessages(this.$route.params.user, this.messagesOffset);
                }
            }
        },
        formatDate(date) {
            return format(parse(date), "d MMM YYYY - HH:mm:ss");
        },
        openLiveChat() {
            window.open("/chat/" + this.$route.params.user, 'newwindow', 'width=500,height=500');
        },
        isUserInHandOverMode(user) {
            window.axios
                .get(`/admin/conversation-log/is-in-hand-over-mode/${user}`)
                .then(response => {
                    this.userInHandOverMode = response.data == true;
                });
        }
    }
}
</script>

<style lang="scss" scoped>
.card {
    height: 75vh;

    .messages {
        overflow: scroll;
        height: calc(100% - 130px);
    }

    .context-log {
        overflow: scroll;
        height: calc(100% - 50px);
    }
}

.message {
    .text {
        border-radius: 6px;
        display: inline-block;
        padding: 7px 10px;
    }

    .them {
        .text {
            background: #eaeaea;
            max-width: 75%;

            img {
                float: left;
                max-width: 300px;
            }
        }
    }

    .me {
        .text {
            background: #4e8cff;
            color: white;
        }
    }

    .list-element {
        display: inline-block;
        width: 100%;
        min-width: 150px;
        border-bottom: 1px solid #999;
        margin-bottom: .5rem;
        padding-bottom: .25rem;

        &:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        img {
            max-width: 100px !important;
        }
    }
}

.loading-indicator {
    text-align: center;
    padding: 20px 0;

    span {
        display: inline-block;
        background-color: #B6B5BA;
        width: 11px;
        height: 11px;
        border-radius: 100%;
        margin-right: 4px;
        animation: bob 2s infinite;
    }
}

.live-chat {
    width: 165px;
    margin: auto;
    height: 2.25rem;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
    line-height: 2.25rem;
    border-radius: .5rem;
    -webkit-box-shadow: 0 2px 4px 0 rgba(0,0,0,.05);
    box-shadow: 0 2px 4px 0 rgba(0,0,0,.05);
}

.live-chat#open {
    background-color: lightgreen;
    cursor: pointer;
}

.live-chat#closed {
    background-color: palevioletred;
}


.open-live-chat:hover {
    text-decoration: underline;
}

.user-name {
    color: grey;
    font-size: smaller;
}

/* SAFARI GLITCH */
.loading-indicator span:nth-child(1) {
    animation-delay: -1s;
}
.loading-indicator span:nth-child(2) {
    animation-delay: -0.85s;
}
.loading-indicator span:nth-child(3) {
    animation-delay: -0.7s;
}

@keyframes bob {
    10% {
        transform: translateY(-10px);
        background-color: #9E9DA2;
    }
    50% {
        transform: translateY(0);
        background-color: #B6B5BA;
    }
}
</style>
