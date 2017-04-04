import OrchestraModel from '../../../../OpenOrchestra/Application/Model/OrchestraModel'
import Fields         from '../../../../OpenOrchestra/Application/Model/Content/Fields'

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
