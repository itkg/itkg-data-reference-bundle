import OrchestraView from '../../../../OpenOrchestra/Application/View/OrchestraView'
import Application   from '../../../../OpenOrchestra/Application/Application'

/**
 * @class ReferenceSummaryView
 */
class ReferenceSummaryView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'well contents clearfix';
    }

    /**
     * Initialize
     * @param {Array}  referenceTypes
     */
    initialize({referenceTypes}) {
        this.referenceTypes = referenceTypes;
    }

    /**
     * Render node tree
     */
    render() {
        let template = this._renderTemplate('Reference/summaryView',
            {
                referenceTypes: this.referenceTypes.models,
                Application: Application
            }
        );
        this.$el.append(template);

        return this;
    }
}

export default ReferenceSummaryView;
