import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import ReferenceType        from '../../Model/ReferenceType/ReferenceType'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import ApplicationError     from '../../../Service/Error/ApplicationError'

/**
 * @class ReferenceTypeFormView
 */
class ReferenceTypeFormView extends mix(AbstractFormView).with(FormViewButtonsMixin) 
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        this.events['click #itkg_reference_type_linkedToSite'] = '_toggleAlwaysShared';
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {string} referenceTypeId
     */
    initialize({form, referenceTypeId = null}) {
        super.initialize({form : form});
        this._referenceTypeId = referenceTypeId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let title = $("input[id*='itkg_reference_type_names_']", this._form.$form).first().val();
        if (null === this._referenceTypeId) {
            title = Translator.trans('open_orchestra_backoffice.table.reference_types.new');
        }
        let template = this._renderTemplate('ReferenceType/referenceTypeEditView', {
            title: title
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();
        this._toggleAlwaysShared();

        return this;
    }

    /**
     * toggle input always shared
     */
    _toggleAlwaysShared() {
        if ($('#itkg_reference_type_linkedToSite:checked', this._$formRegion).length == 0) {
            $('#itkg_reference_type_alwaysShared', this._$formRegion).closest('div.form-group').hide();
        } else {
            $('#itkg_reference_type_alwaysShared', this._$formRegion).closest('div.form-group').show();
        }
    }

   /**
    * Redirect to edit reference type view
    *
    * @param {mixed}  data
    * @param {string} textStatus
    * @param {object} jqXHR
    * @private
    */
   _redirectEditElement(data, textStatus, jqXHR) {
       let referenceTypeId = jqXHR.getResponseHeader('referenceTypeId');
       let url = Backbone.history.generateUrl('editReferenceType', {
          referenceTypeId: referenceTypeId
       });
       Backbone.Events.trigger('form:deactivate', this);
       Backbone.history.navigate(url, true);
    }

   /**
    * Delete reference type
    */
   _deleteElement() {
        if (null === this._referenceTypeId) {
            throw new ApplicationError('Invalid referenceTypeId');
        }
        let referenceType = new ReferenceType({'reference_type_id': this._referenceTypeId});
        referenceType.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listReferenceType');
                Backbone.history.navigate(url, true);
            }
        });
   }
}

export default ReferenceTypeFormView;
