<template>
    <div class="flex flex-wrap -mx-3 mb-3">
        <div class="px-3 mb-6 w-2/3">
            <card class="p-4">
                <heading class="mb-6">Conversation Log</heading>

                <div class="clearfix mb-4">
                    <div class="float-left"><b>Bot</b></div>
                    <div class="float-right"><b>User</b></div>
                </div>

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
                        <div class="text-left">
                            <div class="text">{{ message.message }}</div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="text-right">
                            <div class="text mb-1">{{ message.message }}</div>
                            <div class="text-70">Matched "{{ message.matched_intent }}" Intent</div>
                        </div>
                    </template>
                </div>
            </card>
        </div>

        <div class="px-3 mb-6 w-1/3">
            <card class="p-4">
                <heading class="mb-6">Context Log</heading>
            </card>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            messages: []
        };
    },
    mounted() {
        this.fetchMessages(this.$route.params.user, 0);
    },
    methods: {
        fetchMessages(user, offset) {
            window.axios
                .get(`/admin/conversation-log/conversation-log/${user}/${offset}`)
                .then(response => {
                    response.data.forEach(message => {
                        this.messages.push(message);
                    });
                });
        }
    }
}
</script>

<style lang="scss" scoped>
.message {
    .text {
        border-radius: 5px;
        border: 1px solid rgba(0,0,0,.5);
        display: inline-block;
        padding: 5px 7px;
    }
}
</style>
