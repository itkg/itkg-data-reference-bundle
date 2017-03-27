import AbstractFormView      from '../../../Service/Form/View/AbstractFormView'
import Application           from '../../Application'
import ApplicationError      from '../../../Service/Error/ApplicationError'
import Reference             from '../../Model/Reference/Reference'
import References            from '../../Collection/Reference/References'
import Statuses              from '../../Collection/Status/Statuses'
import FormViewButtonsMixin  from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ReferenceToolbarView  from './ReferenceToolbarView'
//import ReferenceVersionsView from './ReferenceVersionsView'
import FlashMessageBag       from '../../../Service/FlashMessage/FlashMessageBag'

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
//        if (true === this._referenceType.get('defining_versionable') || true === this._referenceType.get('defining_statusable')) {
//            this._renderReferenceActionToolbar($('.reference-action-toolbar', this.$el));
//        }
        super._renderForm();

        // activate tab data
        $('.nav-tabs a.nav-tab-data', this._$formRegion).tab('show');
        $('.tab-reference .tab-pane', this._$formRegion).removeClass('active');
        $('.tab-reference .tab-data', this._$formRegion).addClass('active');
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderReferenceActionToolbar($selector) {
        this._displayLoader($selector);

        let statuses = new Statuses();
        let referenceVersions = new References();
        $.when(
//            statuses.fetch({
//                apiContext: 'reference',
//                urlParameter: {
//                    language: this._reference.get('language'),
//                    referenceId: this._reference.get('reference_id'),
//                    version: this._reference.get('version')
//                }
//            }),
            referenceVersions.fetch({
                apiContext: 'list-version',
                urlParameter: {
                    language: this._reference.get('language'),
                    referenceId: this._reference.get('reference_id')
                }
            })
        ).done( () => {
            let referenceToolbarView = new ReferenceToolbarView({
                referenceVersions: referenceVersions,
//                statuses: statuses,
                reference: this._reference,
                referenceType: this._referenceType
            });
            $selector.html(referenceToolbarView.render().$el);
        });
    }

    /**
     * Manage Version
     * @param {References} referenceVersions
     */
    manageVersion(referenceVersions) {
//        let referenceVersionsView = new ReferenceVersionsView({
//            collection: referenceVersions,
//            referenceId: this._reference.get('reference_id'),
//            language: this._reference.get('language'),
//            referenceTypeId: this._referenceType.get('reference_type_id')
//        });
//        this._$formRegion.html(referenceVersionsView.render().$el);
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
