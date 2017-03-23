import ReferenceSubApplication from './Application/ReferenceSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        ReferenceSubApplication.run();
    });
});
