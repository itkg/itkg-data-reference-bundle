import AbstractCollectionView from '../../../../OpenOrchestra/Service/DataTable/View/AbstractCollectionView'
import ReferenceTypesListView from './ReferenceTypesListView'
import Application            from '../../../../OpenOrchestra/Application/Application'

/**
 * @class ReferenceTypesView
 */
class ReferenceTypesView extends AbstractCollectionView
{
    /**
     * Render reference types view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('itkg_reference.reference_type.title_list'),
                urlAdd: '#' + Backbone.history.generateUrl('newReferenceType')
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('ReferenceType/referenceTypesView', {
                language: Application.getContext().language
            });
            this.$el.html(template);
            this._listView = new ReferenceTypesListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.reference-types-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }
}

export default ReferenceTypesView;
