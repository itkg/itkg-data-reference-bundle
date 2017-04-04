import DataTableCollection from '../../../../OpenOrchestra/Service/DataTable/Collection/DataTableCollection'
import Reference           from '../../Model/Reference/Reference'

/**
 * @class References
 */
class References extends DataTableCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Reference;
    }

    /**
     * @inheritdoc
     */
    toJSON(options) {
        return {
            'references': super.toJSON(options)
        }
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return this._getSyncReadUrl(options, urlParameter);
            case "delete":
                return this._getSyncDeleteUrl(options, urlParameter);
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncReadUrl(options, urlParameter) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "list":
                return Routing.generate('open_orchestra_api_reference_list', urlParameter);
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncDeleteUrl(options, urlParameter) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            default:
                return Routing.generate('open_orchestra_api_reference_delete_multiple');
        }
    }
}

export default References
