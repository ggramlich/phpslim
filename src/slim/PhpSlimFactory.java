package slim;

import fitnesse.slim.Jsr232Bridge;
import fitnesse.slim.SlimFactory;
import fitnesse.slim.StatementExecutorInterface;

public class PhpSlimFactory extends SlimFactory {
  private static Jsr232Bridge phpBridge;
  
  public Jsr232Bridge getBridge() {
    if (null == phpBridge) {
      phpBridge = new PhpBridge();
    }
    return phpBridge;
  }
  
  public StatementExecutorInterface getStatementExecutor() throws Exception {
    return new PhpStatementExecutor(getBridge());
  }
  
}
