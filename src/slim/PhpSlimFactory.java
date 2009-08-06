package slim;

import fitnesse.slim.StatementExecutorInterface;

public class PhpSlimFactory implements SlimFactory {
  Bridge phpBridge;
  
  public Bridge getBridge() {
    if (null == phpBridge) {
      phpBridge = new PhpBridge();
    }
    return phpBridge;
  }
  
  public StatementExecutorInterface getStatementExecutor() throws Exception {
    return new PhpStatementExecutor(getBridge());
  }
  
}
