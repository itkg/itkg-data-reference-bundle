import OrchestraRouter      from '../OrchestraRouter'
import Application          from '../../Application'
import FormBuilder          from '../../../Service/Form/Model/FormBuilder'
import ReferenceSummaryView from '../../View/Reference/ReferenceSummaryView'
import ReferencesView       from '../../View/Reference/ReferencesView'
import ReferenceFormView    from '../../View/Reference/ReferenceFormView'
import NewReferenceFormView from '../../View/Reference/NewReferenceFormView'
import ReferenceTypes       from '../../Collection/ReferenceType/ReferenceTypes'
import References           from '../../Collection/Reference/References'
import ReferenceType        from '../../Model/ReferenceType/ReferenceType'
import Reference            from '../../Model/Reference/Reference'
//import ConfirmModalView   from '../../../Service/ConfirmModal/View/ConfirmModalView'

/**
 * @class ReferenceRouter
 */
class ReferenceRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.routes = {
            'reference/summary'                                      : 'showReferenceSummary',
            'reference/list/:referenceTypeId/:language(/:page)'      : 'listReference',
            'reference/edit/:referenceTypeId/:language/:referenceId' : 'editReference',
            'reference/new/:referenceTypeId/:language'               : 'newReference'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.contribution.title')
            },
            {
                label: Translator.trans('itkg_reference.menu.platform.reference'),
                link: '#'+Backbone.history.generateUrl('showReferenceSummary')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-reference'
        };
    }

    /**
     * show reference summary
     */
    showReferenceSummary() {
        this._displayLoader(Application.getRegion('content'));
        let referenceTypes = new ReferenceTypes();

        referenceTypes.fetch({
            apiContext: 'list_reference_type_for_reference',
            success: () => {
                let referenceSummaryView = new ReferenceSummaryView({
                    referenceTypes: referenceTypes
                });
                let el = referenceSummaryView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }


    /**
     * Edit reference
     *
     * @param {string} referenceTypeId
     * @param {string} language
     * @param {string} referenceId
     */
    editReference(referenceTypeId, language, referenceId) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('itkg_reference_bundle_reference_form', {
            referenceId: referenceId,
            language: language
        });
        let referenceType = new ReferenceType();
        let reference = new Reference({id: referenceId});

        $.when(
            referenceType.fetch({urlParameter: {referenceTypeId: referenceTypeId}}),
            reference.fetch({
                urlParameter: {language: language},
                enabledCallbackError: false
            })
        ).done(() => {
            FormBuilder.createFormFromUrl(url, (form) => {
                    let referenceFormView = new ReferenceFormView({
                        form: form,
                        referenceType: referenceType,
                        reference: reference,
                        siteLanguages: Application.getContext().siteLanguages
                    });
                    Application.getRegion('content').html(referenceFormView.render().$el);
                })
        })
        .fail(() => {
            this._errorCallbackEdit(referenceTypeId, referenceId, language);
        })
    }

    /**
     * Create referenceType
     *
     * @param {string} referenceTypeId
     * @param {string} language
     */
    newReference(referenceTypeId, language) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('itkg_reference_bundle_reference_new', {
            referenceTypeId: referenceTypeId,
            language: language
        });

        FormBuilder.createFormFromUrl(url, (form) => {
            let newReferenceFormView = new NewReferenceFormView({
                form: form,
                referenceTypeId: referenceTypeId,
                language: language,
                siteLanguages: Application.getContext().siteLanguages
            });
            Application.getRegion('content').html(newReferenceFormView.render().$el);
        });
    }

    /**
     * list reference by reference type
     */
    listReference(referenceTypeId, language, page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        let urlParameter = {
            referenceTypeId: referenceTypeId,
            siteId: Application.getContext().siteId,
            language: language
        };

        let referenceType = new ReferenceType();
        let references = new References();

        $.when(
            referenceType.fetch({urlParameter: {referenceTypeId: referenceTypeId}}),
            references.fetch({
                apiContext: 'list',
                urlParameter: urlParameter,
                data : {
                    start: page * pageLength,
                    length: pageLength
                }
            })
        ).done( () => {
            let referencesView = new ReferencesView({
                collection: references,
                settings: {
                    page: page,
                    deferLoading: [references.recordsTotal, references.recordsFiltered],
                    data: references.models,
                    pageLength: pageLength
                },
                urlParameter: urlParameter,
                referenceType: referenceType
            });
            let el = referencesView.render().$el;
            Application.getRegion('content').html(el);
        });
    }

    /**
     * Callback if reference not existing in specific language
     * Show popin to create reference in this language
     *
     * @private
     */
    _errorCallbackEdit(referenceTypeId, referenceId, language) {
//        let noCallback = () => {
//            let url = Backbone.history.generateUrl('listReference',{
//                referenceTypeId: referenceTypeId,
//                language: language
//            });
//            Backbone.history.navigate(url, true);
//        };
//        let yesCallback = () => {
//            new Reference().save({}, {
//                apiContext: 'new-language',
//                urlParameter: {
//                    referenceId: referenceId,
//                    language: language
//                },
//                success: () => {
//                    Backbone.history.loadUrl(Backbone.history.fragment);
//                }
//            })
//        };
//
//        let confirmModalView = new ConfirmModalView({
//            confirmTitle: Translator.trans('open_orchestra_backoffice.reference.confirm_create.title'),
//            confirmMessage: Translator.trans('open_orchestra_backoffice.reference.confirm_create.message'),
//            context: this,
//            yesCallback: yesCallback,
//            noCallback: noCallback
//        });
//
//        Application.getRegion('modal').html(confirmModalView.render().$el);
//        confirmModalView.show();
    }
}

export default ReferenceRouter;
