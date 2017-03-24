import AbstractDataTableView       from '../../../Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../Service/DataTable/Mixin/DeleteCheckboxListViewMixin'
import DuplicateIconListViewMixin  from '../../../Service/DataTable/Mixin/DuplicateIconListViewMixin'
import CellFormatterManager        from '../../../Service/DataFormatter/Manager'
import BooleanFormatter            from '../../../Service/DataFormatter/BooleanFormatter'
import DateFormatter               from '../../../Service/DataFormatter/DateFormatter'

/**
 * @class ReferenceListView
 */
class ReferenceListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin, DuplicateIconListViewMixin)
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
     * @inheritDoc
     */
    getTableId() {
        return 'reference_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        let columnsDefinition = [];
        columnsDefinition.push(this._getColumnsDefinitionDeleteCheckbox());
        columnsDefinition = columnsDefinition.concat(this._generateListableColumn());
        columnsDefinition = columnsDefinition.concat(this._generateFieldColumn());
        columnsDefinition.push(this._getColumnsDefinitionDuplicateIcon());
        columnsDefinition[1].orderDirection = 'desc';
        columnsDefinition[1].createdCell = this._createEditLink;
        
        return columnsDefinition;
    }
    
    /**
     * generate listable columns
     */
    _generateListableColumn() {
        let columnsDefinition = [];
        let defaultListable = this._referenceType.get('default_listable');
        let createdCell = {
            'linked_to_site': BooleanFormatter.getType(),
            'created_at': DateFormatter.getType(),
            'updated_at': DateFormatter.getType()
        };
        for (let column in defaultListable) {
            if (defaultListable[column]) {
                columnsDefinition.push({
                    name: column,
                    title: Translator.trans('itkg_reference.table.references.' + column),
                    orderable: true,
                    activateColvis: true,
                    visible: true,
                    createdCell: createdCell.hasOwnProperty(column) ? CellFormatterManager.format({type: createdCell[column]}) : undefined
                });
            }
        }
        return columnsDefinition;
    }

    /**
     * generate fields columns
     */
    _generateFieldColumn() {
        let columnsDefinition = [];
        let fields = this._referenceType.get('fields');
        for (let field of fields) {
            if (field.listable) {
                columnsDefinition.push({
                    name: "fields." + field.field_id + ".string_value",
                    title: field.label,
                    orderable: field.orderable,
                    activateColvis: true,
                    visible: true,
                    createdCell: CellFormatterManager.format(field)
                });
            }
        }

        return columnsDefinition;
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listReference', {referenceTypeId: this._urlParameter.referenceTypeId, language: this._urlParameter.language, referenceTypeName: this._urlParameter.referenceTypeName, page : page});
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let context = this.data('context');
        let link = Backbone.history.generateUrl('editReference', {
            referenceTypeId: context._referenceType.get('reference_type_id'),
            language: rowData.get('language'),
            referenceId: rowData.get('reference_id'),
            version: rowData.get('version')
        });
//        if (!rowData.get('status').blocked_edition) {
            cellData = $('<a>',{
                text: cellData,
                href: '#'+link
            });
//        }
        $(td).html(cellData)
    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'apiContext': 'list',
            'urlParameter': this._urlParameter
        };
    }
}

export default ReferenceListView;
