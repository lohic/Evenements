package itemRenderers{
	
	import mx.controls.Label;
	import mx.controls.dataGridClasses.*;
	import mx.controls.DataGrid;
	import flash.display.Graphics;
	import mx.styles.StyleManager;
	
	[Style(name="backgroundColor", type="uint", format="Color", inherit="no")]
	
	public class BackgroundComp extends Label {
		
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void
		{
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			
			var g:Graphics = graphics;
			g.clear();
			var grid1:DataGrid = DataGrid(DataGridListData(listData).owner);
			if (grid1.isItemSelected(data) || grid1.isItemHighlighted(data))
				return;
			if (data[DataGridListData(listData).dataField] == 0) 
			{
				g.beginFill(0xFF0000);
			} else {
				g.beginFill(0x00FF00);
			}
			g.drawRect(2, -1, unscaledWidth-4, unscaledHeight+2);
			g.endFill();
		}
	}
}