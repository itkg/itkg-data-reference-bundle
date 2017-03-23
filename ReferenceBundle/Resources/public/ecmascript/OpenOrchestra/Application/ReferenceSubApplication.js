import Application         from '../Application/Application'
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
        this._initRouter();
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
