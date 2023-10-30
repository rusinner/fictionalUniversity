wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  icon: "smiley",
  category: "common",
  attributes: {
    skyColor: {
      type: "string",
      // source: "text",
      // selector: ".skyColor",
    },
    grassColor: {
      type: "string",
      // source: "text",
      // selector: ".grassColor",
    },
  },
  //edit function defines what appears on edit tool
  edit: function (props) {
    function updateSkyColor(e) {
      props.setAttributes({ skyColor: e.target.value });
    }
    function updateGrassColor(e) {
      props.setAttributes({ grassColor: e.target.value });
    }
    return (
      <div>
        <input
          type="text"
          placeholder="sky color"
          value={props.attributes.skyColor}
          onChange={updateSkyColor}
        />
        <input
          type="text"
          placeholder="grass color"
          value={props.attributes.grassColor}
          onChange={updateGrassColor}
        />
      </div>
    );
  },
  //save function defines what appears on actual block
  save: function (props) {
    return null;
  },
});
