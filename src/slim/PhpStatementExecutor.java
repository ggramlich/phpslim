package slim;

import fitnesse.slim.Jsr223Bridge;
import fitnesse.slim.Jsr223StatementExecutor;

public class PhpStatementExecutor extends Jsr223StatementExecutor {

  public PhpStatementExecutor(Jsr223Bridge bridge) throws Exception
  {
    super(bridge);
  }
  
  @Override
  public void setVariable(String name, Object value) {
    // Packing the value into a single element array, because a null value
    // caused the proxy call to hang.
    callMethod("setSymbol", new Object[] {name, new Object[] {value}});
  }
}
