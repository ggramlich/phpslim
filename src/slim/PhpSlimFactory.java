package slim;

import fitnesse.slim.Jsr232Bridge;
import fitnesse.slim.SlimFactory;
import fitnesse.slim.StatementExecutorInterface;

public class PhpSlimFactory extends SlimFactory {
  private static Jsr232Bridge phpBridge;
  private String includePath;
  
  public PhpSlimFactory(String includePath) {
    this.includePath = includePath;
  }
  
  public synchronized Jsr232Bridge getBridge() {
    // Singleton behavior
    if (null == phpBridge) {
      phpBridge = new PhpBridge(includePath);
    }
    return phpBridge;
  }
  
  public void stop() {
    closeBridge();
  }

  private synchronized void closeBridge() {
    getBridge().close();
    phpBridge = null;
  }
  
  public StatementExecutorInterface getStatementExecutor() throws Exception {
    return new PhpStatementExecutor(getBridge());
  }
  
}
