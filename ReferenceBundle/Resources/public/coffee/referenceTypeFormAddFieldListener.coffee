checkReferenceTypeId = (selectorInput) ->

  inputId = $(selectorInput)
  form = $(selectorInput).parents('form')

  dataMessage = form.find("#reference_type_fields").data("prototype-callback-error-message")
  errorMessage = "<div class=\"callback-reference-type-alert alert alert-danger\" role=\"alert\">" + dataMessage + "</div>"
  form.children(".callback-reference-type-alert").remove()

  if not inputId.val().length > 0
    # display error
    form.prepend(errorMessage);
    return false
  return true
