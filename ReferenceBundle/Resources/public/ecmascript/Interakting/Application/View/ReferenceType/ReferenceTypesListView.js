import AbstractDataTableView       from '../../../../OpenOrchestra/Service/DataTable/View/AbstractDataTableView'
import UrlPaginateViewMixin        from '../../../../OpenOrchestra/Service/DataTable/Mixin/UrlPaginateViewMixin'
import DeleteCheckboxListViewMixin from '../../../../OpenOrchestra/Service/DataTable/Mixin/DeleteCheckboxListViewMixin'

/**
 * @class ReferenceTypesListView
 */
class ReferenceTypesListView extends mix(AbstractDataTableView).with(UrlPaginateViewMixin, DeleteCheckboxListViewMixin)
{
    /**
     * @inheritDoc
     */
    getTableId() {
        return 'reference_type_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            this._getColumnsDefinitionDeleteCheckbox(),
            {
                name: "name",
                title: Translator.trans('itkg_reference.reference_type.table.name'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "reference_type_id",
                title: Translator.trans('itkg_reference.reference_type.table.id'),
                orderable: true,
                visibile: true
            }
        ];

    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            'apiContext': 'list'
        };
    }

    /**
     * @inheritDoc
     */
    generateUrlUpdatePage(page) {
        return Backbone.history.generateUrl('listReferenceType', {page : page});
    }

    /**
     *
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editReferenceType', {
            'referenceTypeId': rowData.get('reference_type_id')
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }
}

export default ReferenceTypesListView;
