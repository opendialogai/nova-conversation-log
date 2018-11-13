Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'nova-conversation-log',
            path: '/conversation-log/:user',
            component: require('./components/Tool'),
        },
    ])
})
