import $ from "jquery";

class MyNotes {
  constructor() {
    this.events();
  }

  events() {
    //here i select all elements by id and after that as middle argument i select class because when the page loads for the first time\
    // it can't find the new note if we create one and it need to br refreshed for the buttons to work.So in this way we can overcome this
    $("#my-notes").on("click", ".delete-note", this.deleteNote);
    $("#my-notes").on("click", ".edit-note", this.editNote.bind(this));
    $("#my-notes").on("click", ".update-note", this.updateNote.bind(this));
    $(".submit-note").on("click", this.createNote.bind(this));
  }

  //Methods

  deleteNote = (e) => {
    //target the li tag that the specific delete button is inside.
    var thisNote = $(e.target).parents("li");
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: `${universityData.root_url}/wp-json/wp/v2/note/${thisNote.data(
        "id"
      )}`,
      type: "DELETE",
      success: (response) => {
        thisNote.slideUp();
        if (response.userNoteCount < 5) {
          $(".note-limit-message").removeClass("active");
        }
      },
      error: (response) => {
        console.log(response);
      },
    });
  };

  updateNote = (e) => {
    //target the li tag that the specific delete button is inside.
    var thisNote = $(e.target).parents("li");

    var ourUpatedPost = {
      title: thisNote.find(".note-title-field").val(),
      content: thisNote.find(".note-body-field").val(),
    };
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: `${universityData.root_url}/wp-json/wp/v2/note/${thisNote.data(
        "id"
      )}`,
      type: "POST",
      data: ourUpatedPost,
      success: (response) => {
        this.makeNoteReadOnly(thisNote);
        console.log(response);
      },
      error: (response) => {
        console.log(response);
      },
    });
  };

  editNote = (e) => {
    var thisNote = $(e.target).parents("li");
    if (thisNote.data("state") == "editable") {
      //make read only
      this.makeNoteReadOnly(thisNote);
    } else {
      //make editable
      this.makeNoteEditable(thisNote);
    }
  };

  createNote = () => {
    var ourNewPost = {
      title: $(".new-note-title").val(),
      content: $(".new-note-body").val(),
      status: "publish",
    };
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: `${universityData.root_url}/wp-json/wp/v2/note/`,
      type: "POST",
      data: ourNewPost,
      success: (response) => {
        $(".new-note-title, .new-note-body").val("");
        $(`
        <li data-id="${response.id}">
                    <input readonly class="note-title-field" value="${response.title.raw}">
                    <span class="edit-note"> <i class="fa fa-pencil" area-hidden="true"> </i> Edit</span>
                    <span class="delete-note"> <i class="fa fa-trash-o" area-hidden="true"> </i> Delete</span>

                    <textarea readonly class=" note-body-field">${response.content.raw}</textarea>
                    <span class="update-note btn btn--blue btn--small"> <i class="fa fa-arrow-right" area-hidden="true"> </i> Update</span>

                </li>
        `)
          .prependTo("#my-notes")
          .hide()
          .slideDown();
        console.log(response);
      },
      error: (response) => {
        if (response.responseText === "You have reached your note limit") {
          $(".note-limit-message").addClass("active");
        }
        console.log(response);
      },
    });
  };

  makeNoteEditable = (thisNote) => {
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-times" area-hidden="true"> </i> Cancel');
    thisNote
      .find(".note-title-field, .note-body-field")
      .removeAttr("readonly")
      .addClass("note-active-field");
    thisNote.find(".update-note").addClass("update-note--visible");
    thisNote.data("state", "editable");
  };
  makeNoteReadOnly = (thisNote) => {
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-pencil" area-hidden="true"> </i> Edit');
    thisNote
      .find(".note-title-field, .note-body-field")
      .attr("readonly", "readonly")
      .removeClass("note-active-field");
    thisNote.find(".update-note").removeClass("update-note--visible");
    thisNote.data("state", "cancel");
  };
}

export default MyNotes;
