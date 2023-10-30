wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  icon: "smiley",
  category: "common",
  //edit function defines what appears on edit tool
  edit: function () {
    return (
      <div>
        <p>Hello this is a paragraph.</p>
        <h3>Hi there!</h3>
      </div>
    );
  },
  //save function defines what appears on actual block
  save: function () {
    return (
      <>
        <h3>This is an h3</h3>
        <p>this is a paragraph</p>
      </>
    );
  },
});
