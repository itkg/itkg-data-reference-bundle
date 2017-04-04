import Application         from '../../OpenOrchestra/Application/Application'
import ReferenceTypeRouter from './Router/ReferenceType/ReferenceTypeRouter'
import ReferenceRouter     from './Router/Reference/ReferenceRouter'

/**
 * @class ReferenceSubApplication
 */
class ReferenceSubApplication
{
    /**
     * Run sub Application
     */
    run() {
        this._initConfiguration();
        this._initRouter();
    }

    /**
     * Initialize configuration
     * @private
     */
    _initConfiguration() {
        var user_roles = Application.getContext().user.roles;
        Application.getContext().updateUserAccessSection(
            'reference',
            user_roles.indexOf('ROLE_DEVELOPER') > -1 || user_roles.indexOf('REFERENCE_ADMIN') > -1
        );
    }

    /**
     * Initialize router
     * @private
     */
    _initRouter() {
        new ReferenceTypeRouter();
        new ReferenceRouter();
    }
}

export default (new ReferenceSubApplication);
