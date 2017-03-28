import OrchestraView    from '../OrchestraView'
import Reference        from '../../Model/Reference/Reference'
import ApplicationError from '../../../Service/Error/ApplicationError'
import Application      from '../../Application'

/**
 * @class ReferenceToolbarView
 */
class ReferenceToolbarView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.className = 'container-fluid search-engine';
    }

    /**
     * Initialize
     * @param {Reference}     reference
     * @param {ReferenceType} referenceType
     */
    initialize({reference, referenceType}) {
        this._reference = reference;
        this._referenceType = referenceType;
    }

    /**
     * Render reference toolbar
     */
    render() {
        let template = this._renderTemplate('Reference/referenceToolbarView',
            {
                referenceType: this._referenceType,
                reference: this._reference
            }
        );
        this.$el.html(template);

        return this;
    }
}

export default ReferenceToolbarView;
