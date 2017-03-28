import OrchestraView           from '../OrchestraView'
import Reference               from '../../Model/Reference/Reference'
import ApplicationError        from '../../../Service/Error/ApplicationError'
import ConfirmPublishModalView from '../Statusable/ConfirmPublishModalView'
import Application             from '../../Application'

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
        this.events = {
            'click .dropdown-workflow li a': '_changeStatus'
        }
    }

    /**
     * Initialize
     * @param {Statuses}      statuses
     * @param {Reference}     reference
     * @param {ReferenceType} referenceType
     */
    initialize({statuses, reference, referenceType}) {
        this._statuses = statuses;
        this._reference = reference;
        this._referenceType = referenceType;
    }

    /**
     * Render reference toolbar
     */
    render() {
        let template = this._renderTemplate('Reference/referenceToolbarView',
            {
                statuses: this._statuses.models,
                referenceType: this._referenceType,
                reference: this._reference
            }
        );
        this.$el.html(template);

        return this;
    }

    /**
     * @param {Object} event
     * @private
     */
    _changeStatus(event) {
        let statusId = $(event.currentTarget).attr('data-id');
        let status = this._statuses.findWhere({id: statusId});
        if (typeof status == "undefined") {
            throw new ApplicationError('Status with id '+statusId+ ' not found');
        }

        this._saveUpdateStatus(status);
    }

    /**
     * @param {Status}  status
     * @private
     */
    _saveUpdateStatus(status) {
        let apiContext = 'update_status';
        this._reference.save({'status': status}, {
            apiContext: apiContext,
            success: () => {
                Backbone.history.loadUrl(Backbone.history.fragment);
            }
        });
    }
}

export default ReferenceToolbarView;
