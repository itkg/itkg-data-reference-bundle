import AbstractCollectionView from '../../../Service/DataTable/View/AbstractCollectionView'
import SearchFormGroupManager from '../../../Service/SearchFormGroup/Manager'
import Application            from '../../Application'
import ReferenceListView      from '../../View/Reference/ReferenceListView'

/**
 * @class ReferencesView
 */
class ReferencesView extends AbstractCollectionView
{
    /**
     * @inheritdoc
     */
    initialize({collection, settings, urlParameter, referenceType}) {
        this._urlParameter = urlParameter;
        this._referenceType = referenceType;
        super.initialize({collection, settings});
    }

    /**
     * Render references view
     */
    render() {
        $.datepicker.setDefaults($.datepicker.regional[Application.getContext().language]);
        let template = this._renderTemplate('Reference/referencesView', {
            referenceType: this._referenceType.toJSON(),
            language: this._urlParameter.language,
            siteLanguages: Application.getContext().siteLanguages,
            SearchFormGroupManager: SearchFormGroupManager,
            dateFormat: $.datepicker._defaults.dateFormat
        });
        this.$el.html(template);

        $('.datepicker', this.$el).datepicker({
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>'
        });

        this._listView = new ReferenceListView({
            collection: this._collection,
            settings: this._settings,
            urlParameter: this._urlParameter,
            referenceType: this._referenceType
        });
        $('.references-list', this.$el).html(this._listView.render().$el);

        return this;
    }
}

export default ReferencesView;
