import AbstractFormView     from '../../../../OpenOrchestra/Service/Form/View/AbstractFormView'
import Application          from '../../../../OpenOrchestra/Application/Application'
import ApplicationError     from '../../../../OpenOrchestra/Service/Error/ApplicationError'
import Reference            from '../../Model/Reference/Reference'
import References           from '../../Collection/Reference/References'
import FormViewButtonsMixin from '../../../../OpenOrchestra/Service/Form/Mixin/FormViewButtonsMixin'
import ReferenceToolbarView from './ReferenceToolbarView'
import FlashMessageBag      from '../../../../OpenOrchestra/Service/FlashMessage/FlashMessageBag'
import FlashMessage         from '../../../../OpenOrchestra/Service/FlashMessage/FlashMessage'

/**
 * @class NewReferenceFormView
 */
class NewReferenceFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{
    /**
     * Initialize
     * @param {Form}   form
     * @param {String} referenceTypeId
     * @param {String} language
     * @param {Array}  siteLanguages
     */
    initialize({form, referenceTypeId, language, siteLanguages}) {
        super.initialize({form : form});
        this._referenceTypeId = referenceTypeId;
        this._language = language;
        this._siteLanguages = siteLanguages;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Reference/newReferenceView', {
            referenceTypeId: this._referenceTypeId,
            language: this._language,
            siteLanguages: this._siteLanguages,
            messages: FlashMessageBag.getMessages()
        });
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
        super.render();

        return this;
    }

    /**
     * Redirect to edit reference view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let referenceId = jqXHR.getResponseHeader('referenceId');
        if (null === referenceId) {
            throw new ApplicationError('Invalid referenceId');
        }
        let url = Backbone.history.generateUrl('editReference', {
            referenceTypeId: this._referenceTypeId,
            language: this._language,
            referenceId: referenceId
        });
        if (data != '') {
            let message = new FlashMessage(data, 'success');
            FlashMessageBag.addMessageFlash(message);
        }

        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }
}

export default NewReferenceFormView;
