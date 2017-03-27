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
            'click .dropdown-workflow li a': '_changeStatus',
            'click .btn-new-version': 'newVersionForm',
            'change #select-version': '_changeVersion',
            'click .btn-validate-new-version': '_newVersion'
        }
    }

    /**
     * Initialize
     * @param {References}    referenceVersions
     * @param {Statuses}    statuses
     * @param {Reference}     reference
     * @param {ReferenceType} referenceType
     */
    initialize({referenceVersions, statuses, reference, referenceType}) {
        this._referenceVersions = referenceVersions;
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
                referenceVersions: this._referenceVersions.models,
                statuses: this._statuses.models,
                referenceType: this._referenceType,
                reference: this._reference
            }
        );
        this.$el.html(template);

        return this;
    }

    /**
     * Show input version name to add a new version
     */
    newVersionForm() {
        let versionName = this._reference.get('reference_id');
        let template = this._renderTemplate('Reference/newVersionForm', { versionName: versionName });
        $('.new-version-form-region', this.$el).html(template);
    }

    /**
     * Create a new version
     *
     * @private
     */
    _newVersion() {
        let versionName = $('#version_name', this.$el).val() + '_' + new Date().toLocaleString();
        new Reference().save({version_name: versionName}, {
            apiContext: 'new-version',
            urlParameter: {
                referenceId: this._reference.get('reference_id'),
                language: this._reference.get('language'),
                originalVersion : this._reference.get('version')
            },
            success: () => {
                let url = Backbone.history.generateUrl('editReference', {
                    referenceTypeId: this._referenceType.get('reference_type_id'),
                    language: this._reference.get('language'),
                    referenceId: this._reference.get('reference_id')
                });
                if (url === Backbone.history.fragment) {
                    Backbone.history.loadUrl(url);
                } else {
                    Backbone.history.navigate(url, true);
                }
            }
        })
    }

    /**
     * Change version reference
     *
     * @param {Object} event
     * @private
     */
    _changeVersion(event) {
        let version = $(event.currentTarget).val();
        if (null !== version) {
            let url = Backbone.history.generateUrl('editReference', {
                referenceTypeId: this._referenceType.get('reference_type_id'),
                language: this._reference.get('language'),
                referenceId: this._reference.get('reference_id'),
//                version: version
            });
            Backbone.history.navigate(url, true);
        }
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

        if (true === this._referenceType.get('defining_versionable') && true === status.get('published_state')) {
            let confirmPublishModalView = new ConfirmPublishModalView({
                status: status,
                callbackConfirmPublish: $.proxy(this._saveUpdateStatus, this)
            });
            Application.getRegion('modal').html(confirmPublishModalView.render().$el);
            confirmPublishModalView.show();
        } else {
            this._saveUpdateStatus(status);
        }
    }

    /**
     * @param {Status}  status
     * @param {boolean} saveOldPublishedVersion
     * @private
     */
    _saveUpdateStatus(status, saveOldPublishedVersion = false) {
        let apiContext = 'update_status';
        if (saveOldPublishedVersion) {
            apiContext = 'update_status_with_save_published';
        }
        this._reference.save({'status': status}, {
            apiContext: apiContext,
            success: () => {
                Backbone.history.loadUrl(Backbone.history.fragment);
            }
        });
    }
}

export default ReferenceToolbarView;
