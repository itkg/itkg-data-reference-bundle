import AbstractFormView     from '../../../../OpenOrchestra/Service/Form/View/AbstractFormView'
import Application          from '../../../../OpenOrchestra/Application/Application'
import ApplicationError     from '../../../../OpenOrchestra/Service/Error/ApplicationError'
import Reference            from '../../Model/Reference/Reference'
import References           from '../../Collection/Reference/References'
import FormViewButtonsMixin from '../../../../OpenOrchestra/Service/Form/Mixin/FormViewButtonsMixin'
import ReferenceToolbarView from './ReferenceToolbarView'
import FlashMessageBag      from '../../../../OpenOrchestra/Service/FlashMessage/FlashMessageBag'

/**
 * @class ReferenceFormView
 */
class ReferenceFormView extends mix(AbstractFormView).with(FormViewButtonsMixin) 
{
    /**
     * Initialize
     * @param {Form}        form
     * @param {ReferenceType} referenceType
     * @param {Reference}     reference
     * @param {Array}       siteLanguages
     */
    initialize({form, referenceType, reference, siteLanguages}) {
        super.initialize({form : form});
        this._referenceType = referenceType;
        this._reference = reference;
        this._siteLanguages = siteLanguages;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Reference/referenceEditView', {
            referenceType: this._referenceType,
            reference: this._reference,
            siteLanguages: this._siteLanguages,
            messages: FlashMessageBag.getMessages()
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritDoc
     */
    _renderForm() {
        super._renderForm();

        // activate tab data
        $('.nav-tabs a.nav-tab-data', this._$formRegion).tab('show');
        $('.tab-content .tab-pane', this._$formRegion).removeClass('active');
        $('.tab-content .tab-data', this._$formRegion).addClass('active');
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderReferenceActionToolbar($selector) {
        this._displayLoader($selector);

        let referenceToolbarView = new ReferenceToolbarView({
            reference: this._reference,
            referenceType: this._referenceType
        });
        $selector.html(referenceToolbarView.render().$el);
    }

    /**
     * Delete
     * @param {event} event
     */
    _deleteElement(event) {
        let reference = new Reference({'id': this._reference.get('reference_id')});

        reference.destroy({
            apiContext: 'delete-multiple',
            success: () => {
                let url = Backbone.history.generateUrl('listReference', {
                    referenceTypeId: this._referenceType.get('reference_type_id'),
                    language: this._reference.get('language')
                });
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default ReferenceFormView;
