<template>
    <div class="flex flex-wrap -mx-3 mb-3">
        <div class="px-3 mb-6 w-2/3">
            <card class="p-4">
                <heading class="mb-6">Conversation Log</heading>

                <div class="clearfix mb-4">
                    <div class="float-left"><b>Bot</b></div>
                    <div class="float-right"><b>User</b></div>
                </div>

                <div class="messages" v-on:scroll="onMessagesScroll">
                    <div class="message mb-6" v-for="(message, index) in messages">
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

                        <template v-if="message.author == 'them'">
                            <div class="text-left them">
                                <template v-if="message.type == 'image'">
                                    <div class="text image"><img :src="message.message" /></div>
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
                                <div class="text-70 mt-1">Matched "{{ message.matched_intent }}" Intent</div>
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

export default {
    data() {
        return {
            loading: false,
            messagesOffset: 0,
            messages: [],
            contextLogs: []
        };
    },
    mounted() {
        this.fetchMessages(this.$route.params.user, 0);
        this.fetchContextLog(this.$route.params.user);
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
        }
    }
}
</script>

<style lang="scss" scoped>
.card {
    height: 75vh;

    .messages {
        overflow: scroll;
        height: calc(100% - 85px);
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
