import DataTableCollection from '../../../../OpenOrchestra/Service/DataTable/Collection/DataTableCollection'
import ReferenceType       from '../../Model/ReferenceType/ReferenceType'

/**
 * @class ReferenceTypes
 */
class ReferenceTypes extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = ReferenceType;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'reference_types': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        switch (method) {
            case "read":
                return this._getSyncReadUrl(options);
            case "delete":
                return Routing.generate('open_orchestra_api_reference_type_delete_multiple');
        }
    }

    /**
     * @param {Object} options
     *
     * @returns {string}
     * @private
     */
    _getSyncReadUrl(options) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "list_reference_type_for_reference":
                return Routing.generate('open_orchestra_api_reference_type_list_for_reference');
            case "list":
                return Routing.generate('open_orchestra_api_reference_type_list');
        }
    }
}

export default ReferenceTypes
