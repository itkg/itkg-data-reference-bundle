import OrchestraModel from '../OrchestraModel'

/**
 * @class ReferenceType
 */
class ReferenceType extends OrchestraModel
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.idAttribute = 'reference_type_id';
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_reference_type_show', urlParameter);
            case "delete":
                urlParameter.referenceTypeId = this.get('reference_type_id');
                return Routing.generate('open_orchestra_api_reference_type_delete', urlParameter);
        }
    }
}

export default ReferenceType
