import OrchestraRouter       from '../OrchestraRouter'
import Application           from '../../Application'
import ReferenceTypes        from '../../Collection/ReferenceType/ReferenceTypes'
import FormBuilder           from '../../../Service/Form/Model/FormBuilder'
import ReferenceTypeFormView from '../../View/ReferenceType/ReferenceTypeFormView'
import ReferenceTypesView    from '../../View/ReferenceType/ReferenceTypesView'


/**
 * @class ReferenceTypeRouter
 */
class ReferenceTypeRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'reference-type/list(/:page)'         : 'listReferenceType',
            'reference-type/edit/:referenceTypeId': 'editReferenceType',
            'reference-type/new'                  : 'newReferenceType'
        };
    }

    /**

     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label: Translator.trans('open_orchestra_backoffice.menu.developer.title')
            },
            {
                label: Translator.trans('itkg_reference.menu.developer.reference_type'),
                link: '#' + Backbone.history.generateUrl('listReferenceType')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-reference-type'
        };
    }

    /**
     * Edit referenceType
     *
     * @param {string} referenceTypeId
     */
    editReferenceType(referenceTypeId) {
        this._displayLoader(Application.getRegion('content'));
        let url = Routing.generate('itkg_reference_bundle_reference_type_form', {
            referenceTypeId: referenceTypeId
        });
        FormBuilder.createFormFromUrl(url, (form) => {
            let referenceTypeFormView = new ReferenceTypeFormView({
                form: form,
                referenceTypeId: referenceTypeId
            });
            Application.getRegion('content').html(referenceTypeFormView.render().$el);
        });
    }

    /**
     * Create referenceType
     */
    newReferenceType() {
        alert('new reference type');
//        this._displayLoader(Application.getRegion('content'));
//        let url = Routing.generate('open_orchestra_backoffice_reference_type_new');
//        FormBuilder.createFormFromUrl(url, (form) => {
//            let referenceTypeFormView = new ReferenceTypeFormView({
//                form: form
//            });
//            Application.getRegion('content').html(referenceTypeFormView.render().$el);
//        });
    }

    /**
     * List reference type
     *
     * @param {int} page
     */
    listReferenceType(page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = 10;
        page = Number(page) - 1;
        new ReferenceTypes().fetch({
            apiContext: 'list',
            data : {
                start: page * pageLength,
                length: pageLength
            },
            success: (referenceTypes) => {
                let referenceTypesView = new ReferenceTypesView({
                    collection: referenceTypes,
                    settings: {
                        page: page,
                        deferLoading: [referenceTypes.recordsTotal, referenceTypes.recordsFiltered],
                        data: referenceTypes.models,
                        pageLength: pageLength
                    }
                });
                let el = referenceTypesView.render().$el;
                Application.getRegion('content').html(el);
            }
        });
    }
}

export default ReferenceTypeRouter;
