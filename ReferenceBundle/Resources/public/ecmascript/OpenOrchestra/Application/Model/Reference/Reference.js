import OrchestraModel from '../OrchestraModel'
import Fields         from '../Content/Fields'
import Status         from '../Status/Status'

/**
 * @class Reference
 */
class Reference extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('attributes')) {
            response.fields = new Fields(response.attributes);
        }
        if (response.hasOwnProperty('status')) {
            response.status = new Status(response.status);
        }

        return response;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                urlParameter.referenceId = this.get('id');
                return Routing.generate('open_orchestra_api_reference_show', urlParameter);
            case "create":
                return this._getSyncCreateUrl(options, urlParameter);
            case "update":
                return this._getSyncUpdateUrl(options);
            case "delete":
                urlParameter.referenceId = this.get('id');
                return Routing.generate('open_orchestra_api_reference_delete', urlParameter);
        }
    }

    /**
     * @param {Object} options
     *
     * @returns {string}
     * @private
     */
    _getSyncUpdateUrl(options) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "update_status_with_save_published":
                return Routing.generate('open_orchestra_api_reference_update_status_with_save_published');
            case "update_status":
                return Routing.generate('open_orchestra_api_reference_update_status');
        }
    }

    /**
     * @param {Object} options
     * @param {Object} urlParameter
     *
     * @returns {string}
     * @private
     */
    _getSyncCreateUrl(options, urlParameter) {
        let apiContext = options.apiContext || null;
        switch (apiContext) {
            case "new-language":
                return Routing.generate('open_orchestra_api_reference_new_language', urlParameter);
            default:
                return Routing.generate('open_orchestra_api_reference_duplicate', urlParameter);
        }
    }
}

export default Reference
